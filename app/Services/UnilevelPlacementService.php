<?php

namespace App\Services;

use App\Models\UnilevelNode;
use App\Models\UnilevelPath;
use App\Models\UnilevelSummary;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Servicio de Gestión y Colocación para el Árbol Escalonado (Unilevel).
 * Modela la red de patrocinio directo con anchura ilimitada y profundidad infinita.
 * Utiliza Closure Tables (unilevel_paths) para consultas y propagación de red en O(1).
 */
class UnilevelPlacementService
{
    /**
     * Inicializa al Nodo Maestro Raíz (Root) en el sistema escalonado.
     */
    public function createRoot(User $rootUser): UnilevelNode
    {
        // El nodo raíz unilevel no tiene patrocinador y su nivel base es 1
        $rootNode = UnilevelNode::create([
            'user_id' => $rootUser->id,
            'sponsor_id' => null,
            'level' => 1,
            'path' => "/{$rootUser->id}/",
        ]);

        // Registrar relación reflexiva en la tabla de cierre unilevel
        UnilevelPath::create([
            'ancestor_id' => $rootUser->id,
            'descendant_id' => $rootUser->id,
            'depth' => 0,
        ]);

        // Crear resumen de contadores para el nodo raíz
        UnilevelSummary::create([
            'user_id' => $rootUser->id,
            'direct_sponsors_count' => 0,
            'total_network_members' => 0,
            'personal_points' => 0,
            'group_points' => 0,
        ]);

        return $rootNode;
    }

    /**
     * Coloca a un nuevo afiliado como frontal directo de su patrocinador (Nivel 1).
     *
     * @param  User  $newUser  Nuevo usuario a registrar.
     * @param  User  $sponsor  Patrocinador directo.
     *
     * @throws InvalidArgumentException Si el patrocinador no existe en la red unilevel.
     */
    public function placeAffiliate(User $newUser, User $sponsor): UnilevelNode
    {
        $sponsorNode = UnilevelNode::where('user_id', $sponsor->id)->first();
        if (! $sponsorNode) {
            throw new InvalidArgumentException("El patrocinador [{$sponsor->username}] no se encuentra en el árbol escalonado.");
        }

        // 1. Crear el nodo unilevel del nuevo afiliado
        $newNode = UnilevelNode::create([
            'user_id' => $newUser->id,
            'sponsor_id' => $sponsor->id,
            'level' => $sponsorNode->level + 1,
            'path' => $sponsorNode->path."{$newUser->id}/",
        ]);

        // 2. Inicializar o preservar el resumen de contadores del nuevo afiliado
        UnilevelSummary::firstOrCreate(
            ['user_id' => $newUser->id],
            [
                'direct_sponsors_count' => 0,
                'total_network_members' => 0,
                'personal_points' => 0,
                'group_points' => 0,
            ]
        );

        // 3. Registrar toda la jerarquía en la tabla de cierre (unilevel_paths) en 1 sola consulta SQL
        $this->populateUnilevelPaths($newUser->id, $sponsor->id);

        // 4. Incrementar contador de directos del patrocinador inmediato
        UnilevelSummary::where('user_id', $sponsor->id)->increment('direct_sponsors_count');

        // 5. Incrementar el total de miembros de red para toda la línea ascendente de patrocinio
        $ancestorIds = UnilevelPath::where('descendant_id', $newUser->id)
            ->where('ancestor_id', '!=', $newUser->id)
            ->pluck('ancestor_id');

        if ($ancestorIds->isNotEmpty()) {
            UnilevelSummary::whereIn('user_id', $ancestorIds)->increment('total_network_members');
        }

        return $newNode;
    }

    /**
     * Puebla la tabla de cierre unilevel copiando la ancestría del sponsor en una sola sentencia SQL.
     */
    protected function populateUnilevelPaths(int $newUserId, int $sponsorId): void
    {
        // Hereda todos los ancestros del sponsor con depth + 1 y añade la relación reflexiva (depth = 0)
        DB::statement('
            INSERT INTO unilevel_paths (ancestor_id, descendant_id, depth)
            SELECT ancestor_id, ?, depth + 1
            FROM unilevel_paths
            WHERE descendant_id = ?
            UNION ALL
            SELECT ?, ?, 0
        ', [$newUserId, $sponsorId, $newUserId, $newUserId]);
    }
}
