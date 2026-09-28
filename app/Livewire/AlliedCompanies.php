<?php

namespace App\Livewire;

use App\Models\Banner;
use App\Models\BusinessCategory;
use App\Models\BusinessData;
use App\Models\Country;
use App\Models\StoreType;
use App\Services\SearchSpellCorrector;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app.header')]
#[Title('Empresas Aliadas')]
class AlliedCompanies extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    /* =========================
     | FILTROS
     ========================= */
    public $search = '';

    public $suggestedCorrection = '';

    public $seed;

    public $selectedCategory = '';

    public $selectedSubcategory = '';

    public $selectedStoreType = '';

    public $selectedCountry = 1;

    public $selectedDepartment = '';

    public $selectedCity = '';

    /* =========================
     | COLECCIONES PARA SELECTS
     ========================= */
    public $categories;

    public $subcategories;

    public $countries;

    public $departments;

    public $cities;

    public $storeTypes;

    /* =========================
     | MOUNT
     ========================= */
    public function mount()
    {
        $this->seed = rand(1, 999999);
        $this->categories = BusinessCategory::whereNull('parent_id')->orderBy('name')->get();
        $this->storeTypes = StoreType::orderBy('name')->get();

        $this->subcategories = collect();
        $this->countries = collect();
        $this->departments = collect();
        $this->cities = collect();

        $this->countries = Country::all();

        if ($this->selectedCountry) {
            $this->loadDepartments();
        }
    }

    /* =========================
     | RESET PAGINACIÓN
     ========================= */
    public function updating($property)
    {
        $this->resetPage();
    }

    public $searchAnalysis = [];

    /* ======================================================
     | QUERY BASE (BÚSQUEDA INTELIGENTE EN CASCADA CON RELEVANCIA)
     ====================================================== */
    protected function baseQuery()
    {
        $query = BusinessData::query()
            ->where('is_active', 1);

        $searchTerm = trim($this->search);
        $this->suggestedCorrection = '';
        $this->searchAnalysis = [];

        if ($searchTerm !== '') {
            $analysis = SearchSpellCorrector::analyzeQuery($searchTerm);
            $this->searchAnalysis = $analysis;

            if ($analysis['has_corrections']) {
                $this->suggestedCorrection = $analysis['corrected_query'];
            }

            $tokenGroups = $analysis['token_groups'];

            if (empty($tokenGroups)) {
                // Si solo se ingresaron stopwords o caracteres cortos, buscar la frase exacta directamente
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereHas('business', fn ($bQ) => $bQ->where('name', 'like', '%'.$searchTerm.'%'))
                        ->orWhere('keywords', 'like', '%'.$searchTerm.'%')
                        ->orWhere('description', 'like', '%'.$searchTerm.'%');
                });
            } else {
                // Condición de precisión alta: Coincidencia de la frase completa O de TODOS los grupos de palabras clave (AND)
                $strictCondition = function ($q) use ($tokenGroups, $searchTerm) {
                    $q->where(function ($phraseQ) use ($searchTerm) {
                        $phraseQ->whereHas('business', fn ($bQ) => $bQ->where('name', 'like', '%'.$searchTerm.'%'))
                            ->orWhere('keywords', 'like', '%'.$searchTerm.'%');
                    })->orWhere(function ($allWordsQ) use ($tokenGroups) {
                        foreach ($tokenGroups as $group) {
                            $allWordsQ->where(function ($singleWordQ) use ($group) {
                                foreach ($group as $variant) {
                                    $singleWordQ->orWhereHas('business', fn ($bQ) => $bQ->where('name', 'like', '%'.$variant.'%'))
                                        ->orWhere('keywords', 'like', '%'.$variant.'%')
                                        ->orWhereHas('business.categories', fn ($cQ) => $cQ->where('name', 'like', '%'.$variant.'%'))
                                        ->orWhereHas('cityRelation', fn ($ctQ) => $ctQ->where('name', 'like', '%'.$variant.'%'))
                                        ->orWhere('city', 'like', '%'.$variant.'%')
                                        ->orWhere('description', 'like', '%'.$variant.'%');
                                }
                            });
                        }
                    });
                };

                // Verificar si la búsqueda estricta (AND) produce resultados
                $hasStrictMatches = (clone $query)->where($strictCondition)->exists();

                if ($hasStrictMatches) {
                    $query->where($strictCondition);
                } else {
                    // Fallback tolerante a palabras de más: Coincidencia con cualquiera de los términos clave
                    // La puntuación de relevancia en render() colocará arriba a los comercios con mayor número de coincidencias
                    $query->where(function ($fallbackQ) use ($tokenGroups) {
                        foreach ($tokenGroups as $group) {
                            $fallbackQ->orWhere(function ($singleWordQ) use ($group) {
                                foreach ($group as $variant) {
                                    $singleWordQ->orWhereHas('business', fn ($bQ) => $bQ->where('name', 'like', '%'.$variant.'%'))
                                        ->orWhere('keywords', 'like', '%'.$variant.'%')
                                        ->orWhereHas('business.categories', fn ($cQ) => $cQ->where('name', 'like', '%'.$variant.'%'))
                                        ->orWhereHas('cityRelation', fn ($ctQ) => $ctQ->where('name', 'like', '%'.$variant.'%'))
                                        ->orWhere('city', 'like', '%'.$variant.'%')
                                        ->orWhere('description', 'like', '%'.$variant.'%');
                                }
                            });
                        }
                    });
                }
            }
        }

        // Categoría / Subcategoría
        if ($this->selectedSubcategory) {
            $query->whereHas(
                'business.categories',
                fn ($q) => $q->where('business_categories.id', $this->selectedSubcategory)
            );
        } elseif ($this->selectedCategory) {
            $childIds = BusinessCategory::where('parent_id', $this->selectedCategory)->pluck('id');
            $ids = $childIds->push($this->selectedCategory);

            $query->whereHas(
                'business.categories',
                fn ($q) => $q->whereIn('business_categories.id', $ids)
            );
        }

        // Tipo de tienda
        if ($this->selectedStoreType) {
            $query->whereHas(
                'storeTypes',
                fn ($q) => $q->where('store_type_id', $this->selectedStoreType)
            );
        }

        return $query;
    }

    public function applySuggestedCorrection(): void
    {
        if (! empty($this->suggestedCorrection)) {
            $this->search = $this->suggestedCorrection;
            $this->suggestedCorrection = '';
            $this->resetPage();
        }
    }

    public function selectQuickCategory($categoryId = null)
    {
        if ($this->selectedCategory == $categoryId) {
            $this->selectedCategory = '';
        } else {
            $this->selectedCategory = $categoryId ? (string) $categoryId : '';
        }

        $this->updatedSelectedCategory();
        $this->resetPage();
    }

    /* =========================
     | CARGA DINÁMICA DE FILTROS
     ========================= */

    protected function loadDepartments()
    {
        if (! $this->selectedCountry) {
            $this->departments = collect();

            return;
        }

        $this->departments = $this->baseQuery()
            ->where('country_id', $this->selectedCountry)
            ->whereNotNull('department_id')
            ->select('department_id')
            ->distinct()
            ->with('department')
            ->get()
            ->pluck('department')
            ->filter()
            ->unique('id')
            ->values();
    }

    protected function loadCities()
    {
        if (! $this->selectedDepartment) {
            $this->cities = collect();

            return;
        }

        $this->cities = $this->baseQuery()
            ->where('department_id', $this->selectedDepartment)
            ->whereNotNull('city_id')
            ->select('city_id')
            ->distinct()
            ->with('cityRelation')
            ->get()
            ->pluck('cityRelation')
            ->filter()
            ->unique('id')
            ->values();
    }

    /* ======================================================
     | VALIDACIÓN EN CASCADA (CASOS 2, 3 Y 4)
     ====================================================== */
    protected function validateLocationFilters()
    {
        if ($this->selectedCity) {
            $exists = $this->baseQuery()
                ->where('department_id', $this->selectedDepartment)
                ->where('city_id', $this->selectedCity)
                ->exists();

            if (! $exists) {
                $this->reset('selectedCity');
            }
        }

        // Departamento
        if ($this->selectedDepartment) {
            $exists = $this->baseQuery()
                ->where('country_id', $this->selectedCountry)
                ->where('department_id', $this->selectedDepartment)
                ->exists();

            if (! $exists) {
                $this->reset(['selectedDepartment', 'selectedCity']);
            }
        }

        // ❗ País nunca se reinicia
    }

    /* =========================
     | REACCIONES A CAMBIOS
     ========================= */

    // Caso 1: Categoría
    public function updatedSelectedCategory()
    {
        $this->reset(['selectedSubcategory', 'selectedStoreType']);

        $this->subcategories = BusinessCategory::where('parent_id', $this->selectedCategory)->get();

        $this->validateLocationFilters();
        $this->loadDepartments();
        $this->loadCities();
    }

    // Caso 3: Subcategoría
    public function updatedSelectedSubcategory()
    {
        $this->reset('selectedStoreType');

        $this->validateLocationFilters();
        $this->loadDepartments();
        $this->loadCities();
    }

    // Caso 2: Tipo de tienda
    public function updatedSelectedStoreType()
    {
        $this->validateLocationFilters();
        $this->loadDepartments();
        $this->loadCities();
    }

    // País
    public function updatedSelectedCountry()
    {
        $this->reset(['selectedDepartment', 'selectedCity']);
        $this->loadDepartments();
        $this->loadCities();
    }

    // Departamento
    public function updatedSelectedDepartment()
    {
        $this->reset('selectedCity');
        $this->loadCities();
    }

    // ciudad
    public function updatedSelectedCity()
    {

        $this->loadCities();
    }

    /* =========================
     | LIMPIAR FILTROS
     ========================= */
    public function clearFilters()
    {
        $this->seed = rand(1, 999999);
        $this->reset([
            'search',
            'selectedCategory',
            'selectedSubcategory',
            'selectedStoreType',
            'selectedDepartment',
            'selectedCity',
        ]);

        // País se mantiene si estaba seleccionado
        $this->subcategories = collect();
        $this->departments = collect();
        $this->cities = collect();
    }

    /* =========================
     | RENDER
     ========================= */
    public function render()
    {
        $query = $this->baseQuery()
            ->when(
                $this->selectedCountry,
                fn ($q) => $q->where('country_id', $this->selectedCountry)
            )
            ->when(
                $this->selectedDepartment,
                fn ($q) => $q->where('department_id', $this->selectedDepartment)
            )
            ->when(
                $this->selectedCity,
                fn ($q) => $q->where('city_id', $this->selectedCity)
            )
            ->with(['business.latestLogo', 'storeTypes', 'business.categories', 'cityRelation']);

        $searchTerm = trim($this->search);

        if ($searchTerm !== '' && ! empty($this->searchAnalysis['significant_tokens'])) {
            $scoreParts = [];
            $bindings = [];

            // Puntuación por coincidencia de frase completa en keywords
            $scoreParts[] = '(CASE WHEN keywords LIKE ? THEN 30 ELSE 0 END)';
            $bindings[] = '%'.$searchTerm.'%';

            // Puntuación por cada término significativo en keywords y descripción
            foreach ($this->searchAnalysis['significant_tokens'] as $token) {
                $scoreParts[] = '(CASE WHEN keywords LIKE ? THEN 10 ELSE 0 END)';
                $bindings[] = '%'.$token.'%';

                $scoreParts[] = '(CASE WHEN description LIKE ? THEN 2 ELSE 0 END)';
                $bindings[] = '%'.$token.'%';
            }

            $scoreFormula = implode(' + ', $scoreParts);
            $query->orderByRaw("({$scoreFormula}) DESC", $bindings)->orderBy('id', 'desc');
        } else {
            $query->inRandomOrder($this->seed);
        }

        $businessData = $query->paginate(10);

        $banners = Banner::where('is_active', true)
            ->where('location', 'companies')
            ->orderBy('order', 'asc')
            ->orderBy('id', 'desc')
            ->take(6)
            ->get();

        return view('livewire.allied-companies', [
            'businessData' => $businessData,
            'banners' => $banners,
        ]);
    }
}
