<?php

namespace App\Livewire\Office;

use App\Enums\MlmStatus;
use App\Models\MlmPeriod;
use App\Models\PointTransaction;
use App\Models\User;
use App\Models\UserActivation;
use App\Services\MlmPeriodService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * @property-read User|null $user
 * @property-read object $binarySummary
 * @property-read object $unilevelSummary
 * @property-read bool $canSponsor
 * @property-read bool $isInWaitingRoom
 * @property-read Collection<int, User> $waitingRoomMembers
 * @property-read string $leftLink
 * @property-read string $rightLink
 * @property-read string $leftShareText
 * @property-read string $rightShareText
 * @property-read string $leftWhatsappUrl
 * @property-read string $rightWhatsappUrl
 * @property-read MlmPeriod|null $activePeriod
 * @property-read UserActivation|null $userActivation
 * @property-read bool $isQualified
 * @property-read float $minRequiredPoints
 * @property-read float $personalPoints
 * @property-read float $pointsNeeded
 * @property-read int $qualificationProgress
 */
#[Layout('layouts.app')]
#[Title('Oficina Virtual')]
class Dashboard extends Component
{
    /**
     * Obtiene el usuario autenticado actual.
     */
    #[Computed]
    public function user(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }

    /**
     * Resumen de métricas del árbol binario.
     */
    #[Computed]
    public function binarySummary(): object
    {
        $user = $this->user();

        return $user ? ($user->binarySummary ?? (object) [
            'total_left_members' => 0,
            'total_right_members' => 0,
            'total_left_points' => 0,
            'total_right_points' => 0,
        ]) : (object) [
            'total_left_members' => 0,
            'total_right_members' => 0,
            'total_left_points' => 0,
            'total_right_points' => 0,
        ];
    }

    /**
     * Puntos provenientes de la sala de espera para el usuario autenticado.
     *
     * @return array{L: float, R: float}
     */
    #[Computed]
    public function binaryWaitingPoints(): array
    {
        $user = $this->user();
        if (! $user) {
            return ['L' => 0.0, 'R' => 0.0];
        }

        $records = PointTransaction::join('users', 'point_transactions.from_user_id', '=', 'users.id')
            ->where('point_transactions.user_id', $user->id)
            ->where('users.mlm_status', MlmStatus::WAITING_ROOM)
            ->where('point_transactions.tree_type', 'binary')
            ->groupBy('point_transactions.leg')
            ->selectRaw('point_transactions.leg, SUM(point_transactions.points) as total_waiting')
            ->pluck('total_waiting', 'point_transactions.leg');

        return [
            'L' => (float) ($records['L'] ?? 0.0),
            'R' => (float) ($records['R'] ?? 0.0),
        ];
    }

    /**
     * Puntos unilevel provenientes de la sala de espera para el usuario autenticado.
     */
    #[Computed]
    public function unilevelWaitingPoints(): float
    {
        $user = $this->user();
        if (! $user) {
            return 0.0;
        }

        return (float) PointTransaction::join('users', 'point_transactions.from_user_id', '=', 'users.id')
            ->where('point_transactions.user_id', $user->id)
            ->where('users.mlm_status', MlmStatus::WAITING_ROOM)
            ->where('point_transactions.tree_type', 'unilevel')
            ->sum('point_transactions.points');
    }

    /**
     * Resumen de métricas de la red unilevel.
     */
    #[Computed]
    public function unilevelSummary(): object
    {
        $user = $this->user();

        return $user ? ($user->unilevelSummary ?? (object) [
            'direct_sponsors_count' => 0,
            'total_network_members' => 0,
            'personal_points' => 0,
            'group_points' => 0,
        ]) : (object) [
            'direct_sponsors_count' => 0,
            'total_network_members' => 0,
            'personal_points' => 0,
            'group_points' => 0,
        ];
    }

    /**
     * Determina si el usuario tiene habilitada la función de patrocinar a otros.
     */
    #[Computed]
    public function canSponsor(): bool
    {
        return $this->user()?->canSponsorOthers() ?? false;
    }

    /**
     * Determina si el usuario se encuentra actualmente en la Sala de Espera.
     */
    #[Computed]
    public function isInWaitingRoom(): bool
    {
        return $this->user()?->isInWaitingRoom() ?? false;
    }

    /**
     * Listado de miembros patrocinados en la Sala de Espera (Holding Tank).
     *
     * @return Collection<int, User>
     */
    #[Computed]
    public function waitingRoomMembers(): Collection
    {
        $user = $this->user();
        if (! $user) {
            return new Collection;
        }

        return $user->waitingRoomMembers()
            ->with('unilevelSummary')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Enlace de patrocinio para la pierna izquierda.
     */
    #[Computed]
    public function leftLink(): string
    {
        $user = $this->user();
        if (! $this->canSponsor() || ! $user) {
            return '';
        }

        return url('/register/'.urlencode($user->username).'/left');
    }

    /**
     * Enlace de patrocinio para la pierna derecha.
     */
    #[Computed]
    public function rightLink(): string
    {
        $user = $this->user();
        if (! $this->canSponsor() || ! $user) {
            return '';
        }

        return url('/register/'.urlencode($user->username).'/right');
    }

    /**
     * Mensaje de texto formateado para compartir pierna izquierda.
     */
    #[Computed]
    public function leftShareText(): string
    {
        if (! $this->canSponsor()) {
            return '';
        }

        return "Dale clic al enlace para registrarse 👇\n".$this->leftLink();
    }

    /**
     * Mensaje de texto formateado para compartir pierna derecha.
     */
    #[Computed]
    public function rightShareText(): string
    {
        if (! $this->canSponsor()) {
            return '';
        }

        return "Dale clic al enlace para registrarse 👇\n".$this->rightLink();
    }

    /**
     * URL directa para compartir por WhatsApp en pierna izquierda.
     */
    #[Computed]
    public function leftWhatsappUrl(): string
    {
        if (! $this->canSponsor()) {
            return '';
        }

        return 'https://api.whatsapp.com/send?text='.urlencode($this->leftShareText());
    }

    /**
     * URL directa para compartir por WhatsApp en pierna derecha.
     */
    #[Computed]
    public function rightWhatsappUrl(): string
    {
        if (! $this->canSponsor()) {
            return '';
        }

        return 'https://api.whatsapp.com/send?text='.urlencode($this->rightShareText());
    }

    /**
     * Periodo MLM activo actual.
     */
    #[Computed]
    public function activePeriod(): ?MlmPeriod
    {
        return app(MlmPeriodService::class)->getActivePeriod();
    }

    /**
     * Registro de activación del usuario autenticado.
     */
    #[Computed]
    public function userActivation(): ?UserActivation
    {
        return $this->user()?->activation;
    }

    /**
     * Determina si el usuario se encuentra calificado (ACTIVO) en el periodo.
     */
    #[Computed]
    public function isQualified(): bool
    {
        return $this->userActivation()?->isValidActive() ?? false;
    }

    /**
     * Meta de puntos personales mínimos mensuales requeridos.
     */
    #[Computed]
    public function minRequiredPoints(): float
    {
        $period = $this->activePeriod();

        return (float) ($period ? $period->min_activation_pts : 1.80);
    }

    /**
     * Puntos personales acumulados en el periodo actual.
     */
    #[Computed]
    public function personalPoints(): float
    {
        return (float) ($this->unilevelSummary()->personal_points ?? 0);
    }

    /**
     * Puntos restantes para alcanzar la meta mensual.
     */
    #[Computed]
    public function pointsNeeded(): float
    {
        return max(0.0, round($this->minRequiredPoints() - $this->personalPoints(), 2));
    }

    /**
     * Porcentaje de progreso de calificación (0 a 100).
     */
    #[Computed]
    public function qualificationProgress(): int
    {
        if ($this->isQualified()) {
            return 100;
        }

        if ($this->minRequiredPoints() <= 0) {
            return 100;
        }

        return (int) min(100, round(($this->personalPoints() / $this->minRequiredPoints()) * 100));
    }

    /**
     * Renderiza la vista principal del dashboard de la oficina virtual.
     */
    public function render(): View
    {
        return view('livewire.office.dashboard');
    }
}
