<?php

namespace App\Services;

use App\Models\BinaryNode;
use App\Models\BinaryPath;
use App\Models\BinarySummary;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

/**
 * Servicio de Colocación de Alta Eficiencia para el Árbol Binario MLM.
 * Implementa el algoritmo de colocación extrema exterior (Outer Spillover / Derrame),
 * persistencia en Closure Tables para consultas en O(1) y bloqueos atómicos
 * para prevenir condiciones de carrera (Race Conditions) ante miles de registros concurrentes.
 */
class BinaryPlacementService
{
    /**
     * Registra al Nodo Maestro Raíz (Root) en el sistema binario.
     * Se invoca únicamente cuando la red está vacía.
     */
    public function createRoot(User $rootUser): BinaryNode
    {
        // El nodo raíz no tiene padre, ni pierna asignada, y profundidad es 0
        $rootNode = BinaryNode::create([
            'user_id' => $rootUser->id,
            'parent_id' => null,
            'position' => null,
            'left_child_id' => null,
            'right_child_id' => null,
            'depth' => 0,
            'path' => "/{$rootUser->id}/",
        ]);

        // Registrar la relación reflexiva en la tabla de cierre (Closure Table)
        BinaryPath::create([
            'ancestor_id' => $rootUser->id,
            'descendant_id' => $rootUser->id,
            'depth' => 0,
            'leg' => null,
        ]);

        // Inicializar el resumen de contadores para el nodo raíz
        BinarySummary::create([
            'user_id' => $rootUser->id,
            'total_left_members' => 0,
            'total_right_members' => 0,
            'total_left_points' => 0,
            'total_right_points' => 0,
            'extreme_left_user_id' => null,
            'extreme_right_user_id' => null,
        ]);

        return $rootNode;
    }

    /**
     * Coloca un nuevo afiliado en el árbol binario en la pierna seleccionada ('L' o 'R').
     * Navega hasta la hoja más profunda de la pierna especificada (Derrame exterior).
     *
     * @param  User  $newUser  Nuevo usuario a afiliar.
     * @param  User  $sponsor  Patrocinador directo que afilia.
     * @param  string  $leg  Pierna de colocación ('L' = Izquierda, 'R' = Derecha).
     * @return BinaryNode El nodo binario recién creado.
     *
     * @throws InvalidArgumentException Si la pierna no es válida o el sponsor no está en la red.
     */
    public function placeAffiliate(User $newUser, User $sponsor, string $leg): BinaryNode
    {
        $leg = strtoupper(trim($leg));
        if (! in_array($leg, ['L', 'R'], true)) {
            throw new InvalidArgumentException("La pierna binaria debe ser 'L' (Izquierda) o 'R' (Derecha).");
        }

        // Obtener el nodo binario del patrocinador
        $sponsorNode = BinaryNode::where('user_id', $sponsor->id)->first();
        if (! $sponsorNode) {
            throw new InvalidArgumentException("El patrocinador [{$sponsor->username}] no posee una posición activa en el árbol binario.");
        }

        // BLOQUEO ATÓMICO CONCURRENTE:
        // Se bloquea de forma atómica únicamente la rama de colocación de este patrocinador.
        // Esto permite que miles de afiliados en otras ramas o en la otra pierna se registren
        // en paralelo sin bloqueos globales ni cuellos de botella.
        $lockKey = "mlm_binary_placement_{$sponsor->id}_{$leg}";
        $lock = Cache::lock($lockKey, 15);

        return $lock->block(10, function () use ($newUser, $sponsor, $sponsorNode, $leg) {
            // 1. Obtener o inicializar el resumen del patrocinador
            $sponsorSummary = BinarySummary::firstOrCreate(
                ['user_id' => $sponsor->id],
                [
                    'total_left_members' => 0,
                    'total_right_members' => 0,
                    'total_left_points' => 0,
                    'total_right_points' => 0,
                ]
            );

            // 2. Encontrar el nodo padre disponible en la profundidad extrema de la pierna seleccionada
            $placementParentNode = $this->findDeepestAvailableParent($sponsorNode, $sponsorSummary, $leg);

            // 3. Crear el nuevo nodo binario hijo
            $newNode = BinaryNode::create([
                'user_id' => $newUser->id,
                'parent_id' => $placementParentNode->user_id,
                'position' => $leg,
                'left_child_id' => null,
                'right_child_id' => null,
                'depth' => $placementParentNode->depth + 1,
                'path' => $placementParentNode->path."{$newUser->id}/",
            ]);

            // 4. Actualizar el puntero del nodo padre hacia su nuevo hijo en la pierna correspondiente
            if ($leg === 'L') {
                $placementParentNode->update(['left_child_id' => $newUser->id]);
            } else {
                $placementParentNode->update(['right_child_id' => $newUser->id]);
            }

            // 5. Inicializar el resumen del nuevo afiliado
            BinarySummary::firstOrCreate(
                ['user_id' => $newUser->id],
                [
                    'total_left_members' => 0,
                    'total_right_members' => 0,
                    'total_left_points' => 0,
                    'total_right_points' => 0,
                    'extreme_left_user_id' => null,
                    'extreme_right_user_id' => null,
                ]
            );

            // 6. Registrar en la Closure Table (binary_paths) en una sola operación masiva SQL
            $this->populateBinaryPaths($newUser->id, $placementParentNode->user_id, $leg);

            // 7. Incrementar atómicamente los contadores de afiliados de toda la línea ascendente
            //    y actualizar punteros de extremos exteriores sin consultas N+1
            $this->incrementAncestorsMemberCounts($newNode, $placementParentNode, $leg);

            return $newNode;
        });
    }

    /**
     * Encuentra el nodo binario hoja disponible (padre de colocación) en el extremo exterior
     * de la pierna especificada para un patrocinador.
     *
     * @param  User  $sponsor  Patrocinador del afiliado.
     * @param  string  $leg  Pierna ('L' o 'R').
     * @return BinaryNode|null El nodo padre donde se colocará el nuevo afiliado, o null si el sponsor no tiene posición activa.
     */
    public function findPlacementParent(User $sponsor, string $leg): ?BinaryNode
    {
        $leg = strtoupper(trim($leg));
        if (! in_array($leg, ['L', 'R'], true)) {
            return null;
        }

        $sponsorNode = BinaryNode::where('user_id', $sponsor->id)->first();
        if (! $sponsorNode) {
            return null;
        }

        $sponsorSummary = BinarySummary::firstOrCreate(
            ['user_id' => $sponsor->id],
            [
                'total_left_members' => 0,
                'total_right_members' => 0,
                'total_left_points' => 0,
                'total_right_points' => 0,
            ]
        );

        return $this->findDeepestAvailableParent($sponsorNode, $sponsorSummary, $leg);
    }

    /**
     * Encuentra el nodo disponible en el extremo exterior de la pierna especificada.
     * Utiliza optimización por puntero O(1) y fallback de recorrido indexado ultra-rápido.
     */
    protected function findDeepestAvailableParent(
        BinaryNode $sponsorNode,
        BinarySummary $sponsorSummary,
        string $leg
    ): BinaryNode {
        $directChildId = $leg === 'L' ? $sponsorNode->left_child_id : $sponsorNode->right_child_id;

        // Si el slot directo del patrocinador está libre, el patrocinador es el padre inmediato
        if ($directChildId === null) {
            return $sponsorNode;
        }

        // Optimización O(1): Verificar si el puntero al extremo ya apunta a una hoja libre
        $extremeUserId = $leg === 'L' ? $sponsorSummary->extreme_left_user_id : $sponsorSummary->extreme_right_user_id;
        if ($extremeUserId) {
            $extremeNode = BinaryNode::where('user_id', $extremeUserId)->first();
            if ($extremeNode) {
                $slotAvailable = $leg === 'L' ? ($extremeNode->left_child_id === null) : ($extremeNode->right_child_id === null);
                if ($slotAvailable) {
                    return $extremeNode;
                }
            }
        }

        // Fallback optimizado: En caso de que el puntero no estuviera disponible,
        // cargamos todos los nodos de la pierna exterior en memoria RAM en 1 sola consulta
        $directChildNode = BinaryNode::where('user_id', $directChildId)->first();
        if (! $directChildNode) {
            throw new RuntimeException("Inconsistencia en el árbol binario: no se encontró el nodo para el usuario [{$directChildId}].");
        }

        // Cargamos todos los nodos descendientes de la rama del sponsor en 1 sola consulta indexada
        $branchNodes = BinaryNode::where('path', 'like', "{$directChildNode->path}%")
            ->get(['user_id', 'left_child_id', 'right_child_id'])
            ->keyBy('user_id');

        $currentNode = $directChildNode;
        while (true) {
            $nextChildId = $leg === 'L' ? $currentNode->left_child_id : $currentNode->right_child_id;

            if ($nextChildId === null) {
                return $currentNode;
            }

            if (! isset($branchNodes[$nextChildId])) {
                // Si por alguna anomalía no estuviera en la colección, cargar como rescate puntual
                $currentNode = BinaryNode::where('user_id', $nextChildId)->first();
                if (! $currentNode) {
                    throw new RuntimeException("Inconsistencia en el árbol binario: no se encontró el nodo para el usuario [{$nextChildId}].");
                }
            } else {
                $currentNode = $branchNodes[$nextChildId];
            }
        }
    }

    /**
     * Puebla la tabla de cierre (binary_paths) para el nuevo usuario.
     * Copia en un único INSERT masivo las relaciones de todos los ancestros heredadas del padre.
     */
    protected function populateBinaryPaths(int $newUserId, int $parentId, string $position): void
    {
        // 1. Heredar ancestros del padre: el nuevo usuario queda ubicado en la misma pierna ('L' o 'R')
        //    que tenía su padre con respecto a cada ancestro superior.
        // 2. Relación directa con el padre inmediato en la pierna $position.
        // 3. Relación reflexiva consigo mismo (depth = 0, leg = null).
        DB::statement('
            INSERT INTO binary_paths (ancestor_id, descendant_id, depth, leg)
            SELECT ancestor_id, ?, depth + 1, leg
            FROM binary_paths
            WHERE descendant_id = ? AND ancestor_id != ?
            UNION ALL
            SELECT ?, ?, 1, ?
            UNION ALL
            SELECT ?, ?, 0, NULL
        ', [
            $newUserId, $parentId, $parentId,
            $parentId, $newUserId, $position,
            $newUserId, $newUserId,
        ]);
    }

    /**
     * Incrementa atómicamente la cantidad de afiliados de toda la línea ascendente
     * y actualiza el puntero de extremo (exterior) para los ancestros donde este nuevo
     * afiliado representa la nueva hoja extrema en su respectiva pierna.
     *
     * Ejecuta CERO consultas N+1: toda la navegación ascendente se resuelve en memoria RAM.
     */
    /**
     * Incrementa atómicamente la cantidad de afiliados de toda la línea ascendente
     * y actualiza el puntero de extremo (exterior) para los ancestros correspondientes.
     *
     * Utiliza la ruta materializada del nodo ($newNode->path) y binary_paths para máxima eficiencia sin N+1.
     */
    protected function incrementAncestorsMemberCounts(BinaryNode $newNode, BinaryNode $placementParentNode, string $leg): void
    {
        $newUserId = $newNode->user_id;

        // 1. Ancestros para los cuales este nuevo usuario se encuentra en su pierna izquierda
        $leftAncestors = BinaryPath::where('descendant_id', $newUserId)
            ->where('ancestor_id', '!=', $newUserId)
            ->where('leg', 'L')
            ->pluck('ancestor_id');

        if ($leftAncestors->isNotEmpty()) {
            BinarySummary::whereIn('user_id', $leftAncestors)->increment('total_left_members');

            if ($leg === 'L') {
                $ancestorNodes = BinaryNode::whereIn('user_id', $leftAncestors)
                    ->get(['user_id', 'parent_id', 'position'])
                    ->keyBy('user_id');

                $pureLeftAncestorIds = [];
                $currId = $placementParentNode->user_id;

                while ($currId && isset($ancestorNodes[$currId])) {
                    $pureLeftAncestorIds[] = $currId;
                    $node = $ancestorNodes[$currId];
                    if ($node->position !== 'L') {
                        break;
                    }
                    $currId = $node->parent_id;
                }

                if (! empty($pureLeftAncestorIds)) {
                    BinarySummary::whereIn('user_id', $pureLeftAncestorIds)->update(['extreme_left_user_id' => $newUserId]);
                }
            }
        }

        // 2. Ancestros para los cuales este nuevo usuario se encuentra en su pierna derecha
        $rightAncestors = BinaryPath::where('descendant_id', $newUserId)
            ->where('ancestor_id', '!=', $newUserId)
            ->where('leg', 'R')
            ->pluck('ancestor_id');

        if ($rightAncestors->isNotEmpty()) {
            BinarySummary::whereIn('user_id', $rightAncestors)->increment('total_right_members');

            if ($leg === 'R') {
                $ancestorNodes = BinaryNode::whereIn('user_id', $rightAncestors)
                    ->get(['user_id', 'parent_id', 'position'])
                    ->keyBy('user_id');

                $pureRightAncestorIds = [];
                $currId = $placementParentNode->user_id;

                while ($currId && isset($ancestorNodes[$currId])) {
                    $pureRightAncestorIds[] = $currId;
                    $node = $ancestorNodes[$currId];
                    if ($node->position !== 'R') {
                        break;
                    }
                    $currId = $node->parent_id;
                }

                if (! empty($pureRightAncestorIds)) {
                    BinarySummary::whereIn('user_id', $pureRightAncestorIds)->update(['extreme_right_user_id' => $newUserId]);
                }
            }
        }
    }
}
