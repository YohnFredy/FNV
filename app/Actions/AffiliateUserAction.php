<?php

namespace App\Actions;

use App\DTOs\AffiliationData;
use App\Enums\MlmStatus;
use App\Models\BinaryNode;
use App\Models\UnilevelSummary;
use App\Models\User;
use App\Services\BinaryPlacementService;
use App\Services\UnilevelPlacementService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Acción principal para afiliar y registrar usuarios en el sistema Multinivel.
 * Coordina la creación del usuario en Auth.
 * Si es Master, lo coloca como raíz de ambos árboles.
 * Si es un afiliado regular, lo registra en la Sala de Espera (Holding Tank)
 * para que realice sus compras y califique antes de ocupar posición en los árboles.
 */
class AffiliateUserAction
{
    public function __construct(
        protected BinaryPlacementService $binaryPlacementService,
        protected UnilevelPlacementService $unilevelPlacementService,
    ) {}

    /**
     * Determina si ya existe el Nodo Maestro Raíz en la red multinivel.
     */
    public function isMasterRegistered(): bool
    {
        return BinaryNode::whereNull('parent_id')->exists();
    }

    /**
     * Ejecuta el registro y afiliación integral del usuario.
     *
     * @param  AffiliationData  $data  Datos tipados del nuevo afiliado.
     * @return User El usuario registrado.
     *
     * @throws ValidationException En caso de inconsistencia de patrocinador o pierna.
     */
    public function execute(AffiliationData $data): User
    {
        return DB::transaction(function () use ($data) {
            $isMaster = ! $this->isMasterRegistered();

            // =========================================================================
            // VALIDACIONES SEGÚN EL TIPO DE REGISTRO (Master vs Afiliado)
            // =========================================================================
            $sponsor = null;
            if (! $isMaster) {
                // Si ya existe el Master, es estrictamente obligatorio indicar Patrocinador y Pierna
                if (empty($data->sponsorUsername)) {
                    throw ValidationException::withMessages([
                        'sponsor_username' => ['Debes indicar el nombre de usuario del patrocinador.'],
                    ]);
                }

                $sponsor = User::where('username', $data->sponsorUsername)->first();
                if (! $sponsor) {
                    throw ValidationException::withMessages([
                        'sponsor_username' => ["El patrocinador '{$data->sponsorUsername}' no existe en el sistema."],
                    ]);
                }

                if (! $sponsor->canSponsorOthers()) {
                    throw ValidationException::withMessages([
                        'sponsor_username' => ["El patrocinador '{$data->sponsorUsername}' aún no se encuentra activo en el árbol binario para afiliar nuevos miembros."],
                    ]);
                }

                if (empty($data->binaryLeg) || ! in_array($data->binaryLeg, ['L', 'R'], true)) {
                    throw ValidationException::withMessages([
                        'binary_leg' => ['Debes seleccionar una pierna binaria válida (Izquierda o Derecha).'],
                    ]);
                }
            }

            // =========================================================================
            // 1. CREACIÓN DEL USUARIO BASE EN TABLA USERS
            // =========================================================================
            if ($isMaster) {
                $user = User::create([
                    'name' => $data->name,
                    'username' => $data->username,
                    'email' => $data->email,
                    'password' => Hash::make($data->password),
                    'mlm_status' => MlmStatus::ACTIVE_AFFILIATE,
                    'placed_at' => now(),
                ]);

                // Asignación de Master Root en ambos árboles
                $this->binaryPlacementService->createRoot($user);
                $this->unilevelPlacementService->createRoot($user);
            } else {
                /** @var User $sponsor */
                $user = User::create([
                    'name' => $data->name,
                    'username' => $data->username,
                    'email' => $data->email,
                    'password' => Hash::make($data->password),
                    'mlm_status' => MlmStatus::WAITING_ROOM,
                    'sponsor_id' => $sponsor->id,
                    'preferred_leg' => (string) $data->binaryLeg,
                    'placed_at' => null,
                ]);

                // Inicializar resumen unilevel para acumular puntos personales de compra
                UnilevelSummary::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'direct_sponsors_count' => 0,
                        'total_network_members' => 0,
                        'personal_points' => 0,
                        'group_points' => 0,
                    ]
                );
            }

            return $user;
        }, 5);
    }
}
