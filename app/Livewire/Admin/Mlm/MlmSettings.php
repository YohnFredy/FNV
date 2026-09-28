<?php

namespace App\Livewire\Admin\Mlm;

use App\Models\ActivationPt;
use App\Models\MlmPeriod;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Configuración de Calificación MLM')]
class MlmSettings extends Component
{
    public float $min_pts_monthly = 1.80;

    public bool $first_activation_grace_period_enabled = true;

    public int $grace_period_months = 1;

    public function mount(): void
    {
        $config = ActivationPt::first();

        if ($config) {
            $this->min_pts_monthly = (float) $config->min_pts_monthly;
            $this->first_activation_grace_period_enabled = (bool) $config->first_activation_grace_period_enabled;
            $this->grace_period_months = (int) $config->grace_period_months;
        }
    }

    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'min_pts_monthly' => 'required|numeric|min:0.01|max:999999',
            'first_activation_grace_period_enabled' => 'required|boolean',
            'grace_period_months' => 'required|integer|min:1|max:12',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $config = ActivationPt::firstOrCreate(['id' => 1]);
        $config->update([
            'min_pts_monthly' => $this->min_pts_monthly,
            'first_activation_grace_period_enabled' => $this->first_activation_grace_period_enabled,
            'grace_period_months' => $this->grace_period_months,
        ]);

        // Actualizar el periodo activo en curso si existe
        MlmPeriod::where('status', MlmPeriod::STATUS_ACTIVE)->update([
            'min_activation_pts' => $this->min_pts_monthly,
        ]);

        session()->flash('status', '¡Configuración de calificación MLM actualizada exitosamente!');
    }

    public function render(): View
    {
        return view('livewire.admin.mlm.mlm-settings');
    }
}
