<?php

namespace App\Livewire;

use App\Models\BusinessData;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CompanyShow extends Component
{
    public BusinessData $businessData;

    public function mount(BusinessData $businessData): void
    {
        // Cargar relaciones necesarias
        $this->businessData = $businessData->load([
            'business.images' => function ($query) {
                $query->where('type', 'image');
            },
            'business.latestLogo',
            'business.logos',
            'country',
            'department',
            'cityRelation',
            'storeTypes',
        ]);
    }

    #[Layout('layouts.app.header')]
    public function render()
    {
        $business = $this->businessData->business;
        $title = $business ? "{$business->name} - Empresa Aliada Fornuvi" : 'Empresa Aliada - Fornuvi';

        // Obtener logo de la empresa para Open Graph / WhatsApp preview
        $ogImage = null;
        if ($business) {
            $logo = $business->latestLogo ?? $business->logos->first();
            if ($logo && ! empty($logo->path)) {
                $version = $logo->updated_at ? '?v='.$logo->updated_at->timestamp : '';
                $ogImage = asset('storage/'.ltrim($logo->path, '/')).$version;
            }
        }

        if (! $ogImage) {
            $ogImage = asset('fornuvi.png?v=2');
        }

        // Descripción limpia para meta tags
        $cleanDescription = ! empty($this->businessData->description)
            ? Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($this->businessData->description))), 160)
            : ($business ? "Conoce a {$business->name} en Fornuvi. Descubre sus productos, servicios y beneficios de compra." : 'Empresa aliada en Fornuvi.');

        return view('livewire.company-show')
            ->title($title)
            ->layout('layouts.app.header', [
                'title' => $title,
                'metaDescription' => $cleanDescription,
                'ogTitle' => $title,
                'ogDescription' => $cleanDescription,
                'ogImage' => $ogImage,
                'ogUrl' => route('companies.show', $this->businessData),
            ]);
    }
}
