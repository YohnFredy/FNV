<?php

namespace App\Livewire;

use Livewire\Component;

class PurchasePolicyAndConditions extends Component
{
    public bool $terms = false;

    public function policy()
    {
        $this->terms = true;
    }

    public function render()
    {
        return view('livewire.purchase-policy-and-conditions');
    }
}
