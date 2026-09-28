<?php

namespace App\Services;

use App\Enums\MlmStatus;
use App\Models\BinaryNode;
use App\Models\BinaryPath;
use App\Models\BinarySummary;
use App\Models\PointTransaction;
use App\Models\UnilevelNode;
use App\Models\UnilevelPath;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Servicio de Consulta de Alto Rendimiento para Árboles Multinivel (Binario y Unilevel).
 *
 * Principios de Ingeniería aplicados:
 * 1. Cero Consultas N+1: Todas las jerarquías se resuelven en 1 o 2 consultas indexadas
 *    aprovechando las Tablas de Cierre (Closure Tables) y cargando las relaciones con Eager Loading.
 * 2. Seguridad y Aislamiento de Red: Ningún usuario común puede visualizar nodos por encima
 *    de su posición ni ramas ajenas (crosslines).
 * 3. Ensamble en Memoria (RAM): La jerarquía visual se estructura en PHP a partir de
 *    colecciones indexadas por ID, garantizando respuestas en milisegundos incluso con miles de nodos.
 *
 * @author Agente Director (Arquitectura & Calidad)
 */
class TreeQueryService
{
    /**
     * Valida si el usuario autenticado tiene autorización para visualizar el árbol
     * de un usuario específico en el sistema Binario.
     *
     * Reglas de Seguridad:
     * - Si el target es el mismo usuario autenticado -> PERMITIDO.
     * - Si el usuario autenticado es el Nodo Raíz Master (ID 1) -> PERMITIDO (acceso global de administrador).
     * - Si el target existe como descendiente en la Closure Table `binary_paths` -> PERMITIDO.
     * - En cualquier otro caso (usuarios superiores o ramas ajenas) -> DENEGADO (false).
     *
     * @param  User  $authUser  Usuario actualmente autenticado en la sesión.
     * @param  int  $targetUserId  ID del usuario cuyo árbol se intenta visualizar.
     * @return bool True si está autorizado, False si se bloquea por seguridad.
     */
    public function canAccessBinaryUser(User $authUser, int $targetUserId): bool
    {
        // 1. Acceso a su propia raíz
        if ($authUser->id === $targetUserId) {
            return true;
        }

        // 2. Administrador / Nodo Maestro Raíz tiene visibilidad de toda la organización
        if ($authUser->id === 1) {
            return true;
        }

        // 3. Verificación O(1) indexada en la Closure Table: ¿Es targetUserId un descendiente de authUser?
        return BinaryPath::where('ancestor_id', $authUser->id)
            ->where('descendant_id', $targetUserId)
            ->exists();
    }

    /**
     * Valida si el usuario autenticado tiene autorización para visualizar el árbol
     * de un usuario específico en el sistema Escalonado (Unilevel).
     *
     * @param  User  $authUser  Usuario autenticado.
     * @param  int  $targetUserId  ID del usuario objetivo.
     * @return bool True si está autorizado, False si se bloquea por seguridad.
     */
    public function canAccessUnilevelUser(User $authUser, int $targetUserId): bool
    {
        if ($authUser->id === $targetUserId) {
            return true;
        }

        if ($authUser->id === 1) {
            return true;
        }

        // Verificación O(1) indexada en la Closure Table unilevel
        return UnilevelPath::where('ancestor_id', $authUser->id)
            ->where('descendant_id', $targetUserId)
            ->exists();
    }

    /**
     * Obtiene el ID del padre binario inmediato de un usuario.
     *
     * @return int|null ID del padre o null si es la raíz o no existe.
     */
    public function getBinaryParentId(int $userId): ?int
    {
        $parentId = BinaryNode::where('user_id', $userId)->value('parent_id');

        return $parentId !== null ? (int) $parentId : null;
    }

    /**
     * Obtiene el ID del patrocinador unilevel directo de un usuario.
     *
     * @return int|null ID del patrocinador directo o null si es la raíz o no existe.
     */
    public function getUnilevelSponsorId(int $userId): ?int
    {
        $sponsorId = UnilevelNode::where('user_id', $userId)->value('sponsor_id');

        return $sponsorId !== null ? (int) $sponsorId : null;
    }

    /**
     * Obtiene el ID del usuario en el extremo más profundo de una pierna ('L' o 'R').
     *
     * @param  int  $userId  ID del usuario raíz desde donde buscar el extremo.
     * @param  string  $leg  'L' para pierna izquierda, 'R' para pierna derecha.
     * @return int|null ID del usuario en el extremo o null si no tiene hijos en esa pierna.
     */
    public function getBinaryExtreme(int $userId, string $leg): ?int
    {
        $column = ($leg === 'L') ? 'extreme_left_user_id' : 'extreme_right_user_id';
        $childColumn = ($leg === 'L') ? 'left_child_id' : 'right_child_id';

        // 1. Intentar obtener el puntero precalculado en O(1) desde BinarySummary
        $extremeId = BinarySummary::where('user_id', $userId)->value($column);
        if ($extremeId) {
            return (int) $extremeId;
        }

        // 2. Fallback: Si no está en el summary, verificar si al menos tiene hijo directo
        $node = BinaryNode::where('user_id', $userId)->first(['user_id', 'left_child_id', 'right_child_id']);
        if (! $node || $node->{$childColumn} === null) {
            return null;
        }

        // 3. Recorrer la pierna exterior hasta la última hoja
        $currentId = (int) $node->{$childColumn};
        while ($currentId) {
            $nextNode = BinaryNode::where('user_id', $currentId)->first(['user_id', $childColumn]);
            if (! $nextNode || $nextNode->{$childColumn} === null) {
                return $currentId;
            }
            $currentId = (int) $nextNode->{$childColumn};
        }

        return null;
    }

    /**
     * Genera la lista de migas de pan (Breadcrumbs) para navegar en forma ascendente
     * desde el nodo enfocado actualmente hacia arriba, DETENIÉNDOSE estrictamente en la raíz del usuario autenticado.
     *
     * OPTIMIZACIÓN CLOSURE TABLE:
     * - Resuelve la cadena ascendente en 1 sola consulta SQL indexada O(1) usando `binary_paths`.
     * - Cero N+1: Elimina consultas secuenciales o recursivas.
     *
     * Ejemplo de resultado devuelto:
     * [
     *   ['id' => 1, 'name' => 'Fredy (Tú)', 'username' => 'master', 'is_root' => true],
     *   ['id' => 14, 'name' => 'Carlos', 'username' => 'carlos_99', 'is_root' => false],
     *   ['id' => 45, 'name' => 'Andrea', 'username' => 'andrea_2', 'is_root' => false]
     * ]
     *
     * @param  User  $authUser  Usuario autenticado (tope máximo ascendente permitido).
     * @param  int  $targetUserId  Nodo actualmente enfocado.
     * @return array<int, array{id: int, name: string, username: string, is_root: bool}>
     */
    public function getBinaryBreadcrumbs(User $authUser, int $targetUserId): array
    {
        // 1. Obtener la distancia (depth) entre el usuario autenticado y el target
        $maxDepth = BinaryPath::where('ancestor_id', $authUser->id)
            ->where('descendant_id', $targetUserId)
            ->value('depth');

        // Si no hay ruta directa (por ejemplo si targetUserId no es descendiente)
        if ($maxDepth === null) {
            // El usuario Maestro (ID 1) tiene visibilidad de cualquier rama hasta la raíz
            if ($authUser->id === 1) {
                $maxDepth = BinaryPath::where('descendant_id', $targetUserId)->max('depth');
            }

            if ($maxDepth === null) {
                return [
                    [
                        'id' => $authUser->id,
                        'name' => $authUser->name.' (Tú)',
                        'username' => $authUser->username,
                        'is_root' => true,
                    ],
                ];
            }
        }

        // 2. Traer todos los ancestros en 1 sola consulta SQL indexada con JOIN a users
        $ancestors = User::query()
            ->join('binary_paths', 'users.id', '=', 'binary_paths.ancestor_id')
            ->where('binary_paths.descendant_id', $targetUserId)
            ->where('binary_paths.depth', '<=', $maxDepth)
            ->orderByDesc('binary_paths.depth')
            ->select([
                'users.id',
                'users.name',
                'users.username',
            ])
            ->get();

        $breadcrumbs = [];
        foreach ($ancestors as $ancestor) {
            $isAuthRoot = ($ancestor->id === $authUser->id);

            $breadcrumbs[] = [
                'id' => (int) $ancestor->id,
                'name' => $isAuthRoot ? $ancestor->name.' (Tú)' : $ancestor->name,
                'username' => $ancestor->username,
                'is_root' => $isAuthRoot,
            ];
        }

        // Si la cadena no comienza con el usuario autenticado, aseguramos devolver al menos su raíz
        if (empty($breadcrumbs) || $breadcrumbs[0]['id'] !== $authUser->id) {
            $breadcrumbs = [
                [
                    'id' => $authUser->id,
                    'name' => $authUser->name.' (Tú)',
                    'username' => $authUser->username,
                    'is_root' => true,
                ],
            ];
        }

        return $breadcrumbs;
    }

    /**
     * Genera las migas de pan ascendentes para el Árbol Escalonado (Unilevel).
     *
     * OPTIMIZACIÓN CLOSURE TABLE:
     * - Resuelve la cadena ascendente en 1 sola consulta SQL indexada O(1) usando `unilevel_paths`.
     * - Cero N+1: Elimina consultas secuenciales o recursivas.
     *
     * @param  User  $authUser  Usuario autenticado.
     * @param  int  $targetUserId  Nodo enfocado.
     * @return array<int, array{id: int, name: string, username: string, is_root: bool}>
     */
    public function getUnilevelBreadcrumbs(User $authUser, int $targetUserId): array
    {
        // 1. Obtener la distancia (depth) entre el usuario autenticado y el target
        $maxDepth = UnilevelPath::where('ancestor_id', $authUser->id)
            ->where('descendant_id', $targetUserId)
            ->value('depth');

        if ($maxDepth === null) {
            if ($authUser->id === 1) {
                $maxDepth = UnilevelPath::where('descendant_id', $targetUserId)->max('depth');
            }

            if ($maxDepth === null) {
                return [
                    [
                        'id' => $authUser->id,
                        'name' => $authUser->name.' (Tú)',
                        'username' => $authUser->username,
                        'is_root' => true,
                    ],
                ];
            }
        }

        // 2. Traer todos los ancestros en 1 sola consulta SQL indexada con JOIN a users
        $ancestors = User::query()
            ->join('unilevel_paths', 'users.id', '=', 'unilevel_paths.ancestor_id')
            ->where('unilevel_paths.descendant_id', $targetUserId)
            ->where('unilevel_paths.depth', '<=', $maxDepth)
            ->orderByDesc('unilevel_paths.depth')
            ->select([
                'users.id',
                'users.name',
                'users.username',
            ])
            ->get();

        $breadcrumbs = [];
        foreach ($ancestors as $ancestor) {
            $isAuthRoot = ($ancestor->id === $authUser->id);

            $breadcrumbs[] = [
                'id' => (int) $ancestor->id,
                'name' => $isAuthRoot ? $ancestor->name.' (Tú)' : $ancestor->name,
                'username' => $ancestor->username,
                'is_root' => $isAuthRoot,
            ];
        }

        if (empty($breadcrumbs) || $breadcrumbs[0]['id'] !== $authUser->id) {
            $breadcrumbs = [
                [
                    'id' => $authUser->id,
                    'name' => $authUser->name.' (Tú)',
                    'username' => $authUser->username,
                    'is_root' => true,
                ],
            ];
        }

        return $breadcrumbs;
    }

    /**
     * Obtiene y estructura el Árbol Binario descendente a partir de un usuario raíz enfocado,
     * limitando la profundidad exacta al número de niveles configurado ($maxDepth).
     *
     * OPTIMIZACIÓN CRÍTICA PARA MILES DE USUARIOS:
     * - Consulta únicamente los descendientes cuya distancia (depth) sea menor o igual a $maxDepth
     *   mediante la tabla `binary_paths`.
     * - Carga todos los nodos requeridos en UNA SOLA consulta con Eager Loading de `user` y `binarySummary`.
     * - Reconstruye la jerarquía en memoria sin realizar queries SQL recursivas.
     *
     * @param  int  $rootUserId  ID del usuario que encabezará la visualización actual del árbol.
     * @param  int  $maxDepth  Profundidad máxima de niveles a renderizar (ej. 2, 3, 4 o 5). Por defecto 3.
     * @return array<string, mixed>|null Estructura jerárquica con hijos izquierdo, derecho y ranuras vacías.
     */
    public function getBinaryTree(int $rootUserId, int $maxDepth = 3): ?array
    {
        // 1. Obtener los IDs de todos los descendientes en el rango de profundidad deseado
        /** @var array<int, int> $descendantIds */
        $descendantIds = BinaryPath::where('ancestor_id', $rootUserId)
            ->where('depth', '<=', $maxDepth)
            ->pluck('descendant_id')
            ->all();

        if (empty($descendantIds)) {
            $descendantIds = [$rootUserId];
        }

        // 2. Traer todos los nodos involucrados en 1 sola consulta SQL optimizada
        /** @var Collection<int, BinaryNode> $nodesCollection */
        $nodesCollection = BinaryNode::whereIn('user_id', $descendantIds)
            ->with([
                'user:id,name,username,email,created_at',
                'user.binarySummary',
                'user.unilevelSummary',
            ])
            ->get();

        // Indexar por user_id para acceso instantáneo O(1) en memoria PHP
        $nodesById = $nodesCollection->keyBy('user_id');

        if (! $nodesById->has($rootUserId)) {
            return null;
        }

        // 3. Consultar puntos en Sala de Espera para el desglose transparente (Cero N+1, O(1) memoria)
        $waitingPointsMap = [];
        $waitingRecords = PointTransaction::join('users', 'point_transactions.from_user_id', '=', 'users.id')
            ->whereIn('point_transactions.user_id', $descendantIds)
            ->where('users.mlm_status', MlmStatus::WAITING_ROOM)
            ->where('point_transactions.tree_type', 'binary')
            ->groupBy('point_transactions.user_id', 'point_transactions.leg')
            ->selectRaw('point_transactions.user_id, point_transactions.leg, SUM(point_transactions.points) as total_waiting')
            ->get();

        foreach ($waitingRecords as $wr) {
            $waitingPointsMap[$wr->user_id][$wr->leg] = (float) $wr->total_waiting;
        }

        // 4. Ensamblar el árbol binario recursivamente en memoria hasta $maxDepth
        return $this->buildBinaryNodeArray($rootUserId, $nodesById, $waitingPointsMap, currentDepth: 0, maxDepth: $maxDepth);
    }

    /**
     * Construye recursivamente un nodo binario con sus dos posiciones (Left y Right).
     * Si una posición no tiene usuario asignado, genera un elemento de tipo "empty_slot"
     * para que la interfaz permita afiliar directamente en ese lugar.
     *
     * @param  int  $userId  ID del usuario del nodo actual.
     * @param  Collection<int, BinaryNode>  $nodesById  Diccionario de nodos en memoria.
     * @param  array<int, array<string, float>>  $waitingPointsMap  Mapa de puntos en sala de espera indexado por [userId][leg].
     * @param  int  $currentDepth  Nivel de profundidad relativo al nodo raíz visualizado (0 = raíz).
     * @param  int  $maxDepth  Límite de profundidad a dibujar.
     * @return array<string, mixed>
     */
    private function buildBinaryNodeArray(int $userId, Collection $nodesById, array $waitingPointsMap, int $currentDepth, int $maxDepth): array
    {
        /** @var BinaryNode|null $node */
        $node = $nodesById->get($userId);
        if (! $node) {
            return [];
        }

        $user = $node->user;
        $summary = $user->binarySummary;

        $hasLeftChild = $node->left_child_id !== null;
        $hasRightChild = $node->right_child_id !== null;

        // Verificar si los hijos tienen subdescendientes que continuarían más allá de la vista actual
        $leftChildHasMore = false;
        $rightChildHasMore = false;

        $leftChildData = null;
        $rightChildData = null;

        if ($currentDepth < $maxDepth) {
            // Pierna Izquierda
            if ($hasLeftChild && $node->left_child_id && $nodesById->has($node->left_child_id)) {
                $leftChildData = $this->buildBinaryNodeArray($node->left_child_id, $nodesById, $waitingPointsMap, $currentDepth + 1, $maxDepth);
            } elseif ($hasLeftChild && $node->left_child_id) {
                // Existe en la base de datos pero no fue incluido en este lote de profundidad
                $leftChildHasMore = true;
            } else {
                // Ranura vacía disponible para colocación
                $leftChildData = [
                    'type' => 'empty_slot',
                    'position' => 'L',
                    'parent_id' => $userId,
                    'parent_username' => $user->username,
                    'depth' => $currentDepth + 1,
                ];
            }

            // Pierna Derecha
            if ($hasRightChild && $node->right_child_id && $nodesById->has($node->right_child_id)) {
                $rightChildData = $this->buildBinaryNodeArray($node->right_child_id, $nodesById, $waitingPointsMap, $currentDepth + 1, $maxDepth);
            } elseif ($hasRightChild && $node->right_child_id) {
                // Existe en la base de datos más allá del límite
                $rightChildHasMore = true;
            } else {
                // Ranura vacía disponible para colocación
                $rightChildData = [
                    'type' => 'empty_slot',
                    'position' => 'R',
                    'parent_id' => $userId,
                    'parent_username' => $user->username,
                    'depth' => $currentDepth + 1,
                ];
            }
        } else {
            // Llegamos al límite de profundidad configurado ($maxDepth)
            // Indicamos si existen hijos para mostrar el indicador de "+ Ver más ramas"
            $leftChildHasMore = $hasLeftChild;
            $rightChildHasMore = $hasRightChild;
        }

        $leftMembers = $summary ? $summary->total_left_members : 0;
        $rightMembers = $summary ? $summary->total_right_members : 0;
        $leftPoints = $summary ? (float) $summary->total_left_points : 0.0;
        $rightPoints = $summary ? (float) $summary->total_right_points : 0.0;
        $extremeLeftId = $summary ? $summary->extreme_left_user_id : null;
        $extremeRightId = $summary ? $summary->extreme_right_user_id : null;
        $personalPoints = $user->unilevelSummary ? (float) $user->unilevelSummary->personal_points : 0.0;

        $leftWaiting = (float) ($waitingPointsMap[$userId]['L'] ?? 0.0);
        $rightWaiting = (float) ($waitingPointsMap[$userId]['R'] ?? 0.0);
        $leftPlaced = max(0.0, $leftPoints - $leftWaiting);
        $rightPlaced = max(0.0, $rightPoints - $rightWaiting);

        return [
            'type' => 'user_node',
            'id' => $userId,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'initials' => $user->initials(),
            'position' => $node->position,
            'depth' => $currentDepth,
            'created_at' => $user->created_at?->format('d/m/Y') ?? 'Reciente',
            'summary' => [
                'personal_points' => $personalPoints,
                'left_members' => $leftMembers,
                'right_members' => $rightMembers,
                'left_points' => $leftPoints,
                'right_points' => $rightPoints,
                'left_waiting_points' => $leftWaiting,
                'right_waiting_points' => $rightWaiting,
                'left_placed_points' => $leftPlaced,
                'right_placed_points' => $rightPlaced,
                'extreme_left_id' => $extremeLeftId,
                'extreme_right_id' => $extremeRightId,
            ],
            'left_child' => $leftChildData,
            'right_child' => $rightChildData,
            'has_more_left' => $leftChildHasMore,
            'has_more_right' => $rightChildHasMore,
            'left_child_id' => $node->left_child_id,
            'right_child_id' => $node->right_child_id,
        ];
    }

    /**
     * Obtiene y estructura el Árbol Escalonado (Unilevel) descendente a partir de un usuario raíz,
     * acotado a la profundidad máxima deseada ($maxDepth).
     *
     * @param  int  $rootUserId  ID del usuario raíz de la vista.
     * @param  int  $maxDepth  Nivel de profundidad máxima (por defecto 3 niveles).
     * @return array<string, mixed>|null Estructura jerárquica con colecciones de hijos directos.
     */
    public function getUnilevelTree(int $rootUserId, int $maxDepth = 3): ?array
    {
        // 1. Obtener descendientes indexados dentro del rango de profundidad
        /** @var array<int, int> $descendantIds */
        $descendantIds = UnilevelPath::where('ancestor_id', $rootUserId)
            ->where('depth', '<=', $maxDepth)
            ->pluck('descendant_id')
            ->all();

        if (empty($descendantIds)) {
            $descendantIds = [$rootUserId];
        }

        // 2. Traer todos los nodos unilevel en 1 sola consulta
        /** @var Collection<int, UnilevelNode> $nodesCollection */
        $nodesCollection = UnilevelNode::whereIn('user_id', $descendantIds)
            ->with([
                'user:id,name,username,email,created_at',
                'user.unilevelSummary',
            ])
            ->get();

        $nodesById = $nodesCollection->keyBy('user_id');

        if (! $nodesById->has($rootUserId)) {
            return null;
        }

        // Agrupar los nodos por su patrocinador para navegación O(1) de hijos directos
        $childrenBySponsor = $nodesCollection->groupBy('sponsor_id');

        // Consultar puntos en Sala de Espera para unilevel (Cero N+1, O(1) memoria)
        $waitingUnilevelMap = PointTransaction::join('users', 'point_transactions.from_user_id', '=', 'users.id')
            ->whereIn('point_transactions.user_id', $descendantIds)
            ->where('users.mlm_status', MlmStatus::WAITING_ROOM)
            ->where('point_transactions.tree_type', 'unilevel')
            ->groupBy('point_transactions.user_id')
            ->selectRaw('point_transactions.user_id, SUM(point_transactions.points) as total_waiting')
            ->pluck('total_waiting', 'point_transactions.user_id')
            ->map(fn ($val) => (float) $val)
            ->all();

        return $this->buildUnilevelNodeArray($rootUserId, $nodesById, $childrenBySponsor, $waitingUnilevelMap, currentDepth: 0, maxDepth: $maxDepth);
    }

    /**
     * Construye recursivamente un nodo del árbol escalonado con todos sus directos (frontales).
     *
     * @param  int  $userId  ID del usuario actual.
     * @param  Collection<int, UnilevelNode>  $nodesById  Diccionario de nodos.
     * @param  \Illuminate\Support\Collection<int|string, Collection<int, UnilevelNode>>  $childrenBySponsor  Hijos indexados por sponsor_id.
     * @param  array<int, float>  $waitingUnilevelMap  Mapa de puntos en sala de espera unilevel por [userId].
     * @param  int  $currentDepth  Nivel actual.
     * @param  int  $maxDepth  Nivel máximo.
     * @return array<string, mixed>
     */
    private function buildUnilevelNodeArray(
        int $userId,
        Collection $nodesById,
        \Illuminate\Support\Collection $childrenBySponsor,
        array $waitingUnilevelMap,
        int $currentDepth,
        int $maxDepth
    ): array {
        /** @var UnilevelNode|null $node */
        $node = $nodesById->get($userId);
        if (! $node) {
            return [];
        }

        $user = $node->user;
        $summary = $user->unilevelSummary;

        $childrenData = [];
        $hasMoreChildren = false;

        /** @var Collection<int, UnilevelNode> $directChildren */
        $directChildren = $childrenBySponsor->get($userId, collect());

        if ($currentDepth < $maxDepth) {
            foreach ($directChildren as $childNode) {
                if ($nodesById->has($childNode->user_id)) {
                    $childrenData[] = $this->buildUnilevelNodeArray(
                        $childNode->user_id,
                        $nodesById,
                        $childrenBySponsor,
                        $waitingUnilevelMap,
                        $currentDepth + 1,
                        $maxDepth
                    );
                }
            }
        } else {
            // Si está en el límite y tiene hijos registrados, se activa la bandera de expansión
            $hasMoreChildren = ($summary ? $summary->direct_sponsors_count : 0) > 0 || $directChildren->isNotEmpty();
        }

        $directCount = $summary ? $summary->direct_sponsors_count : count($childrenData);
        $totalMembers = $summary ? $summary->total_network_members : 0;
        $personalPoints = $summary ? (float) $summary->personal_points : 0.0;
        $groupPoints = $summary ? (float) $summary->group_points : 0.0;

        $groupWaiting = (float) ($waitingUnilevelMap[$userId] ?? 0.0);
        $groupPlaced = max(0.0, $groupPoints - $groupWaiting);

        return [
            'type' => 'unilevel_node',
            'id' => $userId,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'initials' => $user->initials(),
            'depth' => $currentDepth,
            'level' => $node->level,
            'created_at' => $user->created_at?->format('d/m/Y') ?? 'Reciente',
            'summary' => [
                'direct_sponsors_count' => $directCount,
                'total_network_members' => $totalMembers,
                'personal_points' => $personalPoints,
                'group_points' => $groupPoints,
                'group_waiting_points' => $groupWaiting,
                'group_placed_points' => $groupPlaced,
            ],
            'children' => $childrenData,
            'has_more_children' => $hasMoreChildren,
        ];
    }

    /**
     * Búsqueda en vivo de afiliados descendientes dentro de la red del usuario autenticado.
     * ESTRICTAMENTE PROTEGIDO: Solo busca entre los descendientes reales del $authUser.
     *
     * @param  User  $authUser  Usuario autenticado que realiza la búsqueda.
     * @param  string  $treeType  'binary' o 'unilevel'.
     * @param  string  $term  Término de búsqueda (nombre o @username).
     * @param  int  $limit  Límite de resultados a retornar.
     * @return array<int, array{id: int, name: string, username: string, depth: int}>
     */
    public function searchDownline(User $authUser, string $treeType, string $term, int $limit = 8): array
    {
        $term = trim($term);
        if (mb_strlen($term) < 2) {
            return [];
        }

        // Si es el Master (ID 1), puede buscar en toda la red
        if ($authUser->id === 1) {
            /** @var Collection<int, User> $users */
            $users = User::where(function ($q) use ($term) {
                $q->where('username', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%");
            })
                ->take($limit)
                ->get(['id', 'name', 'username']);

            return $users->map(fn (User $u) => [
                'id' => $u->id,
                'name' => $u->name,
                'username' => $u->username,
                'depth' => 0,
            ])->all();
        }

        // Para usuarios regulares, consultar únicamente a través de la Closure Table respectiva
        $table = ($treeType === 'binary') ? 'binary_paths' : 'unilevel_paths';

        $descendants = DB::table($table)
            ->where('ancestor_id', $authUser->id)
            ->join('users', "{$table}.descendant_id", '=', 'users.id')
            ->where(function ($q) use ($term) {
                $q->where('users.username', 'like', "%{$term}%")
                    ->orWhere('users.name', 'like', "%{$term}%");
            })
            ->select(['users.id', 'users.name', 'users.username', "{$table}.depth"])
            ->take($limit)
            ->get();

        return $descendants->map(fn ($row) => [
            'id' => (int) $row->id,
            'name' => (string) $row->name,
            'username' => (string) $row->username,
            'depth' => (int) $row->depth,
        ])->all();
    }

    /**
     * Obtiene los detalles completos de un afiliado para desplegarlos en el modal informativo.
     *
     * @param  int  $userId  ID del usuario.
     * @return array<string, mixed>|null
     */
    public function getUserDetails(int $userId): ?array
    {
        /** @var User|null $user */
        $user = User::with([
            'binaryNode.parentUser',
            'binarySummary',
            'unilevelNode.sponsorUser',
            'unilevelSummary',
        ])->find($userId);

        if (! $user) {
            return null;
        }

        $binaryNode = $user->binaryNode;
        $binarySummary = $user->binarySummary;
        $unilevelNode = $user->unilevelNode;
        $unilevelSummary = $user->unilevelSummary;

        $leftMembers = $binarySummary ? $binarySummary->total_left_members : 0;
        $rightMembers = $binarySummary ? $binarySummary->total_right_members : 0;
        $leftPoints = $binarySummary ? (float) $binarySummary->total_left_points : 0.0;
        $rightPoints = $binarySummary ? (float) $binarySummary->total_right_points : 0.0;

        $unilevelLevel = $unilevelNode ? $unilevelNode->level : 1;
        $directsCount = $unilevelSummary ? $unilevelSummary->direct_sponsors_count : 0;
        $networkMembers = $unilevelSummary ? $unilevelSummary->total_network_members : 0;
        $personalPoints = $unilevelSummary ? (float) $unilevelSummary->personal_points : 0.0;
        $groupPoints = $unilevelSummary ? (float) $unilevelSummary->group_points : 0.0;

        $waitingBinary = PointTransaction::join('users', 'point_transactions.from_user_id', '=', 'users.id')
            ->where('point_transactions.user_id', $userId)
            ->where('users.mlm_status', MlmStatus::WAITING_ROOM)
            ->where('point_transactions.tree_type', 'binary')
            ->groupBy('point_transactions.leg')
            ->selectRaw('point_transactions.leg, SUM(point_transactions.points) as total_waiting')
            ->pluck('total_waiting', 'point_transactions.leg');

        $leftWaiting = (float) ($waitingBinary['L'] ?? 0.0);
        $rightWaiting = (float) ($waitingBinary['R'] ?? 0.0);

        $groupWaiting = (float) PointTransaction::join('users', 'point_transactions.from_user_id', '=', 'users.id')
            ->where('point_transactions.user_id', $userId)
            ->where('users.mlm_status', MlmStatus::WAITING_ROOM)
            ->where('point_transactions.tree_type', 'unilevel')
            ->sum('point_transactions.points');

        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'initials' => $user->initials(),
            'created_at' => $user->created_at?->format('d/m/Y H:i') ?? 'N/A',
            'binary' => [
                'position' => $binaryNode?->position,
                'parent_username' => $binaryNode?->parentUser?->username,
                'parent_name' => $binaryNode?->parentUser?->name,
                'personal_points' => $personalPoints,
                'left_members' => $leftMembers,
                'right_members' => $rightMembers,
                'left_points' => $leftPoints,
                'right_points' => $rightPoints,
                'left_waiting_points' => $leftWaiting,
                'right_waiting_points' => $rightWaiting,
                'left_placed_points' => max(0.0, $leftPoints - $leftWaiting),
                'right_placed_points' => max(0.0, $rightPoints - $rightWaiting),
                'has_left_child' => $binaryNode?->left_child_id !== null,
                'has_right_child' => $binaryNode?->right_child_id !== null,
            ],
            'unilevel' => [
                'level' => $unilevelLevel,
                'sponsor_username' => $unilevelNode?->sponsorUser?->username,
                'sponsor_name' => $unilevelNode?->sponsorUser?->name,
                'direct_sponsors_count' => $directsCount,
                'total_network_members' => $networkMembers,
                'personal_points' => $personalPoints,
                'group_points' => $groupPoints,
                'group_waiting_points' => $groupWaiting,
                'group_placed_points' => max(0.0, $groupPoints - $groupWaiting),
            ],
        ];
    }
}
