<?php

namespace Database\Seeders;

use App\Actions\AffiliateUserAction;
use App\DTOs\AffiliationData;
use App\Models\BinaryNode;
use App\Models\BinarySummary;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder para generar afiliados de prueba en los árboles Binario y Escalonado.
 *
 * Funcionamiento:
 * 1. Crea o detecta al usuario Master (Nodo Raíz).
 * 2. Itera hasta alcanzar la cantidad configurada en `$totalAffiliates`.
 * 3. En cada iteración, selecciona un patrocinador de forma aleatoria entre todos
 *    los usuarios registrados hasta ese instante (por ejemplo, para el usuario 50
 *    elige al azar entre los IDs del 1 al 49).
 * 4. Analiza la pierna con menos afiliados del patrocinador elegido (pierna débil / balance):
 *    - Si la pierna izquierda tiene menos afiliados -> asigna 'L'.
 *    - Si la pierna derecha tiene menos afiliados -> asigna 'R'.
 *    - Si tienen la misma cantidad -> escoge una aleatoriamente.
 * 5. Coloca al nuevo afiliado en el extremo más profundo de esa pierna en el binario
 *    y como frontal directo en el unilevel del patrocinador.
 */
class AffiliateSeeder extends Seeder
{
    /**
     * =========================================================================
     * ⚙️ CONFIGURACIÓN: CANTIDAD DE AFILIADOS A CREAR
     * =========================================================================
     * Modifica este número por la cifra que desees probar: 50, 100, 200, 500, etc.
     */
    public int $totalAffiliates = 1;

    /**
     * Ejecuta el sembrado de afiliados en la base de datos.
     */
    public function run(): void
    {
        $faker = Faker::create('es_ES');
        $affiliateAction = app(AffiliateUserAction::class);

        $this->command->info("🚀 Iniciando generación de afiliados de prueba (Meta: {$this->totalAffiliates} afiliados)...");

        // ---------------------------------------------------------------------
        // PASO 1: Asegurar que el Usuario Master (Nodo Raíz) existe
        // ---------------------------------------------------------------------
        $masterUser = null;
        if (! $affiliateAction->isMasterRegistered()) {
            $this->command->info('👑 Creando el Nodo Maestro Raíz del sistema...');

            $masterData = new AffiliationData(
                name: 'fredy',
                username: 'master',
                email: 'fredy.guapacha@gmail.com',
                password: '123',
            );

            $masterUser = $affiliateAction->execute($masterData);
            $this->command->info("✅ Nodo Maestro creado exitosamente: [ID: {$masterUser->id}] @{$masterUser->username}");
        } else {
            // Si ya existe el nodo raíz, obtenerlo
            $rootNode = BinaryNode::whereNull('parent_id')->first();
            if ($rootNode) {
                $masterUser = User::find($rootNode->user_id);
                $this->command->info("ℹ️ Nodo Maestro existente detectado: [ID: {$masterUser?->id}] @{$masterUser?->username}");
            }
        }

        // ---------------------------------------------------------------------
        // PASO 2: Obtener la lista de usuarios ya registrados en la red
        // ---------------------------------------------------------------------
        /** @var array<int, int> $availableSponsorIds */
        $availableSponsorIds = BinaryNode::pluck('user_id')->all();

        $currentCount = count($availableSponsorIds);
        $remainingToCreate = max(0, $this->totalAffiliates - $currentCount);

        if ($remainingToCreate === 0) {
            $this->command->warn("Ya existen {$currentCount} usuarios en la red (igual o superior a la meta de {$this->totalAffiliates}). No se crearon nuevos.");

            return;
        }

        $this->command->info("📊 Usuarios actuales en red: {$currentCount}. Se crearán {$remainingToCreate} nuevos afiliados...");

        // ---------------------------------------------------------------------
        // PASO 3: Bucle de creación y balanceo de afiliados
        // ---------------------------------------------------------------------
        $creados = 0;
        $inicio = $currentCount + 1;
        $fin = $this->totalAffiliates;

        for ($numero = $inicio; $numero <= $fin; $numero++) {
            // 1. Escoger patrocinador aleatorio entre los registrados hasta este momento
            $randomIndex = array_rand($availableSponsorIds);
            $sponsorId = $availableSponsorIds[$randomIndex];

            /** @var User|null $sponsor */
            $sponsor = User::find($sponsorId);
            if (! $sponsor) {
                continue;
            }

            // 2. Consultar el resumen binario del patrocinador para balancear la pierna con menos afiliados
            $summary = BinarySummary::firstOrCreate(
                ['user_id' => $sponsor->id],
                [
                    'total_left_members' => 0,
                    'total_right_members' => 0,
                    'total_left_points' => 0,
                    'total_right_points' => 0,
                ]
            );

            $leftCount = $summary->total_left_members;
            $rightCount = $summary->total_right_members;

            // Decidir la pierna con menor cantidad de afiliados
            if ($leftCount < $rightCount) {
                $chosenLeg = 'L'; // Pierna izquierda tiene menos afiliados
            } elseif ($rightCount < $leftCount) {
                $chosenLeg = 'R'; // Pierna derecha tiene menos afiliados
            } else {
                // Si tienen exactamente la misma cantidad, elegir aleatoriamente
                $chosenLeg = $faker->randomElement(['L', 'R']);
            }

            // 3. Generar credenciales únicas para el nuevo afiliado
            $name = $faker->name();
            $baseUsername = 'afiliado_'.$numero;
            $username = $baseUsername;
            $email = "afiliado_{$numero}@mlmtest.com";

            // Asegurar unicidad de username y email
            $contador = 1;
            while (DB::table('users')->where('username', $username)->exists()) {
                $username = $baseUsername.'_'.$contador;
                $contador++;
            }

            $afiliadoData = new AffiliationData(
                name: $name,
                username: $username,
                email: $email,
                password: '123',
                sponsorUsername: $sponsor->username,
                binaryLeg: $chosenLeg,
            );

            // 4. Ejecutar la afiliación (colocación en profundidad binaria + unilevel frontal)
            $nuevoUsuario = $affiliateAction->execute($afiliadoData);

            // 5. Agregar el nuevo usuario a la lista de patrocinadores disponibles para los siguientes
            $availableSponsorIds[] = $nuevoUsuario->id;
            $creados++;

            // 6. Mensaje de progreso detallado en consola cada 10 registros o el primero
            if ($creados === 1 || $creados % 10 === 0 || $numero === $fin) {
                $piernaNombre = $chosenLeg === 'L' ? 'Izquierda (L)' : 'Derecha (R)';
                $this->command->line(
                    "👤 [{$numero}/{$fin}] Nuevo: @{$username} -> Patrocinador: [ID: {$sponsor->id}] @{$sponsor->username} | Pierna asignada: {$piernaNombre} (Izq: {$leftCount} vs Der: {$rightCount})"
                );
            }
        }

        $this->command->info("🎉 ¡Sembrado completado! Se crearon {$creados} nuevos afiliados exitosamente.");
    }
}
