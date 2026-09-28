<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder para importar la base de datos histórica / legacy (sql_base)
 * reconstruyendo con precisión matemática las estructuras de árboles
 * MLM: Binario (binary_nodes, binary_paths, binary_summaries)
 * y Unilevel (unilevel_nodes, unilevel_paths, unilevel_summaries),
 * así como los usuarios, datos de perfil y catálogos geográficos.
 */
class LegacyDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Iniciando importación y reestructuración multinivel desde sql_base...');
        $sqlDir = base_path('sql_base');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Limpieza de tablas existentes para garantizar idempotencia
        $this->command->line('🧹 Limpiando tablas previas...');
        $tablesToTruncate = [
            'parishes', 'cities', 'departments', 'countries', 'document_types',
            'user_data', 'binary_paths', 'binary_summaries', 'binary_nodes',
            'unilevel_paths', 'unilevel_summaries', 'unilevel_nodes', 'users',
        ];
        foreach ($tablesToTruncate as $tbl) {
            DB::table($tbl)->truncate();
        }

        // 2. Importar Catálogos Geográficos
        $this->command->line('🌍 Importando Países, Departamentos, Ciudades y Parroquias...');
        $this->extractAndExecuteInserts($sqlDir.'/countries.sql', 'countries');
        $this->extractAndExecuteInserts($sqlDir.'/departments.sql', 'departments');
        $this->extractAndExecuteInserts($sqlDir.'/cities.sql', 'cities');
        $this->extractAndExecuteInserts($sqlDir.'/parishes.sql', 'parishes');

        // 3. Tipos de Documento
        $this->command->line('🪪 Creando Tipos de Documento...');
        DB::table('document_types')->insertOrIgnore([
            ['id' => 1, 'country_id' => 1, 'name' => 'Cédula de Ciudadanía', 'code' => 'CC', 'is_default' => 1, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'country_id' => 1, 'name' => 'Tarjeta de Identidad', 'code' => 'TI', 'is_default' => 0, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'country_id' => 1, 'name' => 'Cédula de Ciudadanía', 'code' => 'CC', 'is_default' => 0, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'country_id' => 1, 'name' => 'Cédula de Extranjería', 'code' => 'CE', 'is_default' => 0, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'country_id' => 1, 'name' => 'Pasaporte', 'code' => 'PAS', 'is_default' => 0, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'country_id' => 1, 'name' => 'NIT', 'code' => 'NIT', 'is_default' => 0, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'country_id' => 1, 'name' => 'Registro Civil', 'code' => 'RC', 'is_default' => 0, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'country_id' => 1, 'name' => 'Permiso Especial de Permanencia', 'code' => 'PEP', 'is_default' => 0, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'country_id' => 1, 'name' => 'Permiso por Protección Temporal', 'code' => 'PPT', 'is_default' => 0, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 4. Importar Usuarios y Datos de Perfil
        $this->command->line('👥 Importando Usuarios y Perfiles...');
        $this->extractAndExecuteInserts($sqlDir.'/users.sql', 'users');
        $this->extractAndExecuteInserts($sqlDir.'/user_data (2).sql', 'user_data');

        // 5. Reconstrucción del Árbol Binario
        $this->command->line('🌳 Reconstruyendo Árbol Binario (binary_nodes, binary_paths, binary_summaries)...');
        $this->seedBinaryTree($sqlDir.'/binaries.sql');

        // 6. Reconstrucción del Árbol Unilevel
        $this->command->line('🪜 Reconstruyendo Árbol Escalonado (unilevel_nodes, unilevel_paths, unilevel_summaries)...');
        $this->seedUnilevelTree($sqlDir.'/unilevels.sql');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $userCount = DB::table('users')->count();
        $binaryCount = DB::table('binary_nodes')->count();
        $unilevelCount = DB::table('unilevel_nodes')->count();

        $this->command->info('🎉 ¡Importación completada exitosamente!');
        $this->command->info("   - Total Usuarios: {$userCount}");
        $this->command->info("   - Nodos Binarios: {$binaryCount}");
        $this->command->info("   - Nodos Unilevel: {$unilevelCount}");
    }

    /**
     * Extrae y ejecuta sentencias INSERT INTO de un archivo SQL preservando el delimitador de fin de sentencia.
     */
    protected function extractAndExecuteInserts(string $filePath, string $tableName): void
    {
        if (! file_exists($filePath)) {
            $this->command->warn("Archivo no encontrado: {$filePath}");

            return;
        }

        $content = file_get_contents($filePath);
        $pattern = '/INSERT INTO [`\']?'.preg_quote($tableName, '/').'[`\']?\s*.*?(?:;\r?\n|\);\Z)/s';
        preg_match_all($pattern, $content, $matches);

        foreach ($matches[0] as $insertSql) {
            DB::unprepared($insertSql);
        }
    }

    /**
     * Construye y siembra los nodos binarios, tabla de cierre y resúmenes con derrame exacto.
     */
    protected function seedBinaryTree(string $binariesPath): void
    {
        $binContent = file_get_contents($binariesPath);
        preg_match_all("/INSERT INTO `binaries`.*?VALUES\s*(.*?);/s", $binContent, $matchesBin);
        $binRows = [];
        foreach ($matchesBin[1] as $valBlock) {
            preg_match_all("/\((\d+),\s*(\d+),\s*(\d+),\s*'([^']+)'(?:,\s*[^,)]+)*\)/", $valBlock, $rms, PREG_SET_ORDER);
            foreach ($rms as $rm) {
                $binRows[] = [
                    'id' => (int) $rm[1],
                    'user_id' => (int) $rm[2],
                    'parent_id' => (int) $rm[3],
                    'side' => $rm[4],
                ];
            }
        }

        // 1. Inicializar Nodo Maestro Raíz (ID: 1)
        $binaryNodes = [
            1 => [
                'user_id' => 1,
                'parent_id' => null,
                'position' => null,
                'depth' => 0,
                'path' => '/1/',
                'left_child_id' => null,
                'right_child_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // 2. Colocar cada nodo en su posición padre/hijo exacta
        $pendingBin = $binRows;
        while (! empty($pendingBin)) {
            $progress = false;
            foreach ($pendingBin as $idx => $r) {
                $pId = $r['parent_id'];
                if (isset($binaryNodes[$pId])) {
                    $uId = $r['user_id'];
                    $pos = $r['side'] === 'left' ? 'L' : 'R';
                    $pNode = $binaryNodes[$pId];
                    $binaryNodes[$uId] = [
                        'user_id' => $uId,
                        'parent_id' => $pId,
                        'position' => $pos,
                        'depth' => $pNode['depth'] + 1,
                        'path' => $pNode['path'].$uId.'/',
                        'left_child_id' => null,
                        'right_child_id' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    if ($pos === 'L') {
                        $binaryNodes[$pId]['left_child_id'] = $uId;
                    } else {
                        $binaryNodes[$pId]['right_child_id'] = $uId;
                    }
                    unset($pendingBin[$idx]);
                    $progress = true;
                }
            }
            if (! $progress) {
                break;
            }
        }

        // 3. Generar la Closure Table (binary_paths) en orden topológico por profundidad
        $nodesByDepth = [];
        foreach ($binaryNodes as $uId => $node) {
            $nodesByDepth[$node['depth']][] = $node;
        }
        ksort($nodesByDepth);

        $binaryPaths = [];
        $binaryPathRows = [];
        foreach ($nodesByDepth as $depth => $nodes) {
            foreach ($nodes as $node) {
                $uId = $node['user_id'];
                $pId = $node['parent_id'];
                $pos = $node['position'];

                // Relación reflexiva
                $binaryPathRows[] = ['ancestor_id' => $uId, 'descendant_id' => $uId, 'depth' => 0, 'leg' => null];
                $binaryPaths[$uId][$uId] = ['depth' => 0, 'leg' => null];

                if ($pId !== null) {
                    // Padre directo
                    $binaryPathRows[] = ['ancestor_id' => $pId, 'descendant_id' => $uId, 'depth' => 1, 'leg' => $pos];
                    $binaryPaths[$pId][$uId] = ['depth' => 1, 'leg' => $pos];

                    // Ancestros superiores
                    foreach ($binaryPaths as $aId => $descMap) {
                        if ($aId !== $pId && isset($descMap[$pId])) {
                            $legOfParent = $descMap[$pId]['leg'];
                            $depthToParent = $descMap[$pId]['depth'];
                            $binaryPathRows[] = [
                                'ancestor_id' => $aId,
                                'descendant_id' => $uId,
                                'depth' => $depthToParent + 1,
                                'leg' => $legOfParent,
                            ];
                            $binaryPaths[$aId][$uId] = [
                                'depth' => $depthToParent + 1,
                                'leg' => $legOfParent,
                            ];
                        }
                    }
                }
            }
        }

        // 4. Calcular Binary Summaries (miembros por pierna y extremos exteriores O(1))
        $binarySummaries = [];
        foreach ($binaryNodes as $uId => $node) {
            $leftCount = 0;
            $rightCount = 0;
            if (isset($binaryPaths[$uId])) {
                foreach ($binaryPaths[$uId] as $dId => $info) {
                    if ($info['depth'] > 0) {
                        if ($info['leg'] === 'L') {
                            $leftCount++;
                        } elseif ($info['leg'] === 'R') {
                            $rightCount++;
                        }
                    }
                }
            }

            // Extremo exterior izquierdo
            $extLeft = null;
            if ($node['left_child_id'] !== null) {
                $curr = $node['left_child_id'];
                while ($binaryNodes[$curr]['left_child_id'] !== null) {
                    $curr = $binaryNodes[$curr]['left_child_id'];
                }
                $extLeft = $curr;
            }

            // Extremo exterior derecho
            $extRight = null;
            if ($node['right_child_id'] !== null) {
                $curr = $node['right_child_id'];
                while ($binaryNodes[$curr]['right_child_id'] !== null) {
                    $curr = $binaryNodes[$curr]['right_child_id'];
                }
                $extRight = $curr;
            }

            $binarySummaries[] = [
                'user_id' => $uId,
                'total_left_members' => $leftCount,
                'total_right_members' => $rightCount,
                'total_left_points' => 0,
                'total_right_points' => 0,
                'extreme_left_user_id' => $extLeft,
                'extreme_right_user_id' => $extRight,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // 5. Inserción masiva optimizada por lotes
        foreach (array_chunk(array_values($binaryNodes), 500) as $chunk) {
            DB::table('binary_nodes')->insert($chunk);
        }

        foreach (array_chunk($binaryPathRows, 1000) as $chunk) {
            DB::table('binary_paths')->insert($chunk);
        }

        foreach (array_chunk($binarySummaries, 500) as $chunk) {
            DB::table('binary_summaries')->insert($chunk);
        }
    }

    /**
     * Construye y siembra los nodos unilevel, closure table y resúmenes.
     */
    protected function seedUnilevelTree(string $unilevelsPath): void
    {
        $uniContent = file_get_contents($unilevelsPath);
        preg_match_all("/INSERT INTO `unilevels`.*?VALUES\s*(.*?);/s", $uniContent, $matchesUni);
        $uniRows = [];
        foreach ($matchesUni[1] as $valBlock) {
            preg_match_all("/\((\d+),\s*(\d+),\s*(\d+)(?:,\s*[^,)]+)*\)/", $valBlock, $rms, PREG_SET_ORDER);
            foreach ($rms as $rm) {
                $uniRows[] = [
                    'id' => (int) $rm[1],
                    'user_id' => (int) $rm[2],
                    'sponsor_id' => (int) $rm[3],
                ];
            }
        }

        // 1. Nodo Maestro Raíz
        $unilevelNodes = [
            1 => [
                'user_id' => 1,
                'sponsor_id' => null,
                'level' => 1,
                'path' => '/1/',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // 2. Colocar cada afiliado con su sponsor
        $pendingUni = $uniRows;
        while (! empty($pendingUni)) {
            $progress = false;
            foreach ($pendingUni as $idx => $r) {
                $sId = $r['sponsor_id'];
                if (isset($unilevelNodes[$sId])) {
                    $uId = $r['user_id'];
                    $sNode = $unilevelNodes[$sId];
                    $unilevelNodes[$uId] = [
                        'user_id' => $uId,
                        'sponsor_id' => $sId,
                        'level' => $sNode['level'] + 1,
                        'path' => $sNode['path'].$uId.'/',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    unset($pendingUni[$idx]);
                    $progress = true;
                }
            }
            if (! $progress) {
                break;
            }
        }

        // 3. Closure Table (unilevel_paths)
        $uniNodesByLevel = [];
        foreach ($unilevelNodes as $uId => $node) {
            $uniNodesByLevel[$node['level']][] = $node;
        }
        ksort($uniNodesByLevel);

        $unilevelPaths = [];
        $unilevelPathRows = [];
        foreach ($uniNodesByLevel as $lvl => $nodes) {
            foreach ($nodes as $node) {
                $uId = $node['user_id'];
                $sId = $node['sponsor_id'];

                $unilevelPathRows[] = ['ancestor_id' => $uId, 'descendant_id' => $uId, 'depth' => 0];
                $unilevelPaths[$uId][$uId] = 0;

                if ($sId !== null) {
                    foreach ($unilevelPaths as $aId => $descMap) {
                        if (isset($descMap[$sId])) {
                            $d = $descMap[$sId] + 1;
                            $unilevelPathRows[] = ['ancestor_id' => $aId, 'descendant_id' => $uId, 'depth' => $d];
                            $unilevelPaths[$aId][$uId] = $d;
                        }
                    }
                }
            }
        }

        // 4. Resúmenes Unilevel
        $directCounts = [];
        foreach ($unilevelNodes as $uId => $node) {
            if ($node['sponsor_id'] !== null) {
                $directCounts[$node['sponsor_id']] = ($directCounts[$node['sponsor_id']] ?? 0) + 1;
            }
        }

        $unilevelSummaries = [];
        foreach ($unilevelNodes as $uId => $node) {
            $totalMembers = 0;
            if (isset($unilevelPaths[$uId])) {
                foreach ($unilevelPaths[$uId] as $dId => $d) {
                    if ($d > 0) {
                        $totalMembers++;
                    }
                }
            }
            $unilevelSummaries[] = [
                'user_id' => $uId,
                'direct_sponsors_count' => $directCounts[$uId] ?? 0,
                'total_network_members' => $totalMembers,
                'personal_points' => 0,
                'group_points' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // 5. Inserción masiva por lotes
        foreach (array_chunk(array_values($unilevelNodes), 500) as $chunk) {
            DB::table('unilevel_nodes')->insert($chunk);
        }

        foreach (array_chunk($unilevelPathRows, 1000) as $chunk) {
            DB::table('unilevel_paths')->insert($chunk);
        }

        foreach (array_chunk($unilevelSummaries, 500) as $chunk) {
            DB::table('unilevel_summaries')->insert($chunk);
        }
    }
}
