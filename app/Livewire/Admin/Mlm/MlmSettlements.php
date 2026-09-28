<?php

namespace App\Livewire\Admin\Mlm;

use App\Models\MlmPeriod;
use App\Models\User;
use App\Services\MlmPeriodService;
use App\Services\MlmSettlementService;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Liquidaciones y Cierres Mensuales MLM')]
class MlmSettlements extends Component
{
    public bool $showPreviewModal = false;

    public bool $showConfirmSettlementModal = false;

    public bool $showManualActivationModal = false;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $previewData = null;

    // Campos para activación manual
    public string $userSearch = '';

    public ?int $selectedUserId = null;

    public string $manualAdminNotes = '';

    public string $manualExpiration = 'end_of_month';

    public function mount(): void {}

    #[Computed]
    public function activePeriod(): MlmPeriod
    {
        return app(MlmPeriodService::class)->getActivePeriod();
    }

    /**
     * @return Collection<int, MlmPeriod>
     */
    #[Computed]
    public function settledPeriods(): Collection
    {
        return MlmPeriod::where('status', MlmPeriod::STATUS_SETTLED)
            ->withCount('userBalances')
            ->orderBy('id', 'desc')
            ->limit(12)
            ->get();
    }

    /**
     * @return Collection<int, User>
     */
    #[Computed]
    public function searchResults(): Collection
    {
        if (strlen(trim($this->userSearch)) < 2) {
            return collect();
        }

        return User::query()
            ->with(['activation', 'unilevelSummary'])
            ->where('username', 'like', "%{$this->userSearch}%")
            ->orWhere('name', 'like', "%{$this->userSearch}%")
            ->orWhere('email', 'like', "%{$this->userSearch}%")
            ->limit(8)
            ->get();
    }

    public function selectUser(int $userId): void
    {
        $this->selectedUserId = $userId;
    }

    public function openManualActivationModal(): void
    {
        $this->userSearch = '';
        $this->selectedUserId = null;
        $this->manualAdminNotes = '';
        $this->showManualActivationModal = true;
    }

    public function applyManualActivation(): void
    {
        if (! $this->selectedUserId) {
            return;
        }

        $user = User::findOrFail($this->selectedUserId);
        $admin = auth()->user();

        $expiresAt = $this->manualExpiration === 'end_of_next_month'
            ? CarbonImmutable::now()->addMonthNoOverflow()->endOfMonth()
            : CarbonImmutable::now()->endOfMonth();

        app(MlmPeriodService::class)->manuallyActivateByAdmin(
            user: $user,
            admin: $admin,
            expiresAt: $expiresAt,
            notes: $this->manualAdminNotes ?: 'Activación manual por panel de administración'
        );

        $this->showManualActivationModal = false;
        $this->selectedUserId = null;
        $this->userSearch = '';
        $this->manualAdminNotes = '';

        session()->flash('status', "¡El usuario @{$user->username} ha sido activado exitosamente!");
    }

    public function deactivateUser(int $userId): void
    {
        $user = User::findOrFail($userId);
        app(MlmPeriodService::class)->deactivateUser($user);

        session()->flash('status', "Activación revocada para @{$user->username}.");
    }

    public function runPreview(): void
    {
        $settlementService = app(MlmSettlementService::class);
        $this->previewData = $settlementService->previewSettlement($this->activePeriod);
        $this->showPreviewModal = true;
    }

    public function executeSettlement(): void
    {
        $settlementService = app(MlmSettlementService::class);
        $result = $settlementService->executeSettlement($this->activePeriod);

        $this->showConfirmSettlementModal = false;
        $this->showPreviewModal = false;
        $this->previewData = null;

        session()->flash('status', "¡Periodo {$result['period_name']} liquidado exitosamente con Flush Total! Nuevo periodo activo: {$result['new_period_code']}.");
    }

    public function render(): View
    {
        return view('livewire.admin.mlm.mlm-settlements');
    }
}
