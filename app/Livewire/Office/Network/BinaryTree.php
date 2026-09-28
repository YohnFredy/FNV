<?php

namespace App\Livewire\Office\Network;

use App\Models\User;
use App\Services\TreeQueryService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Componente Livewire 4 Full-Page para la Visualización Interactiva del Árbol Binario.
 *
 * Características implementadas:
 * - Renderizado acotado en memoria mediante TreeQueryService (Cero N+1).
 * - Protección perimetral de red: Un usuario jamás puede ver ancestros por encima de su raíz ni ramas ajenas.
 * - Navegación descendente (drill-down con clic en tarjetas) y ascendente (migas de pan).
 * - Selector de niveles de profundidad (2, 3, 4 o 5 niveles).
 * - Búsqueda en vivo de afiliados descendientes con salto directo.
 * - Salto instantáneo a los extremos exteriores (Pierna Izquierda / Derecha).
 * - Modal emergente con ficha técnica del afiliado seleccionado.
 */
#[Layout('layouts.app')]
#[Title('Árbol Binario')]
class BinaryTree extends Component
{
    /**
     * ID del usuario cuya genealogía se está visualizando como raíz en la pantalla.
     */
    #[Url(as: 'user', history: true)]
    public int $targetUserId = 0;

    /**
     * Niveles de profundidad a dibujar simultáneamente (2 a 5).
     * Por defecto 3 niveles (15 nodos teóricos), óptimo para renderizado ultrarrápido y limpio.
     */
    #[Url(as: 'depth')]
    public int $depth = 3;

    /**
     * Texto para búsqueda en vivo de afiliados en la organización descendente.
     */
    public string $searchQuery = '';

    /**
     * ID del afiliado seleccionado para desplegar el modal de detalles completos.
     */
    public ?int $selectedUserId = null;

    /**
     * Mensaje flash temporal para notificaciones de seguridad o avisos de red.
     */
    public ?string $statusMessage = null;

    /**
     * Inicialización del componente.
     */
    public function mount(): void
    {
        /** @var User $authUser */
        $authUser = auth()->user();

        // Validar niveles de profundidad permitidos
        if ($this->depth < 2 || $this->depth > 5) {
            $this->depth = 3;
        }

        // Si no se solicitó un usuario específico, arrancar en la propia raíz del afiliado
        if ($this->targetUserId <= 0) {
            $this->targetUserId = $authUser->id;

            return;
        }

        // Comprobación de seguridad perimetral de red
        /** @var TreeQueryService $service */
        $service = app(TreeQueryService::class);
        if (! $service->canAccessBinaryUser($authUser, $this->targetUserId)) {
            $this->statusMessage = __('No tienes permisos para ver la organización de este usuario.');
            $this->targetUserId = $authUser->id;
        }
    }

    /**
     * Cambia el nivel de profundidad visualizado (de 2 a 5 niveles).
     */
    public function setDepth(int $depth): void
    {
        if ($depth >= 2 && $depth <= 5) {
            $this->depth = $depth;
        }
    }

    /**
     * Reenfoca la raíz del árbol en un afiliado descendiente (drill-down interactivo).
     */
    public function focusNode(int $userId): void
    {
        /** @var User $authUser */
        $authUser = auth()->user();
        /** @var TreeQueryService $service */
        $service = app(TreeQueryService::class);

        if ($service->canAccessBinaryUser($authUser, $userId)) {
            $this->targetUserId = $userId;
            $this->searchQuery = '';
            $this->selectedUserId = null;
            $this->statusMessage = null;
        } else {
            $this->statusMessage = __('No tienes permisos: El afiliado seleccionado no pertenece a tu red descendente.');
        }
    }

    /**
     * Sube un nivel hacia el padre en el árbol binario.
     */
    public function goUpOneLevel(): void
    {
        /** @var User $authUser */
        $authUser = auth()->user();

        // Si ya está en su propia raíz, no puede subir más
        if ($this->targetUserId === $authUser->id) {
            return;
        }

        /** @var TreeQueryService $service */
        $service = app(TreeQueryService::class);
        $parentId = $service->getBinaryParentId($this->targetUserId);

        if ($parentId && $service->canAccessBinaryUser($authUser, $parentId)) {
            $this->targetUserId = $parentId;
            $this->selectedUserId = null;
        } else {
            // Si el padre está por encima de su red, volver a su raíz segura
            $this->targetUserId = $authUser->id;
        }
    }

    /**
     * Vuelve instantáneamente a la raíz del usuario logueado.
     */
    public function resetToMyTree(): void
    {
        $this->targetUserId = (int) auth()->id();
        $this->selectedUserId = null;
        $this->searchQuery = '';
        $this->statusMessage = null;
    }

    /**
     * Salta al extremo más profundo de la pierna indicada ('L' o 'R').
     */
    public function goToExtreme(string $leg): void
    {
        /** @var User $authUser */
        $authUser = auth()->user();
        /** @var TreeQueryService $service */
        $service = app(TreeQueryService::class);

        $extremeUserId = $service->getBinaryExtreme($this->targetUserId, $leg);

        if ($extremeUserId && $service->canAccessBinaryUser($authUser, $extremeUserId)) {
            $this->targetUserId = $extremeUserId;
            $this->selectedUserId = null;
        }
    }

    /**
     * Abre el modal / bottom-sheet con la ficha técnica completa del afiliado.
     */
    public function openUserDetails(int $userId): void
    {
        /** @var User $authUser */
        $authUser = auth()->user();
        /** @var TreeQueryService $service */
        $service = app(TreeQueryService::class);

        if ($service->canAccessBinaryUser($authUser, $userId)) {
            $this->selectedUserId = $userId;
        }
    }

    /**
     * Cierra el modal de ficha técnica.
     */
    public function closeUserDetails(): void
    {
        $this->selectedUserId = null;
    }

    /**
     * Estructura arbórea binaria completa calculada reactivamente en memoria.
     *
     * @return array<string, mixed>|null
     */
    #[Computed]
    public function tree(): ?array
    {
        /** @var TreeQueryService $service */
        $service = app(TreeQueryService::class);

        return $service->getBinaryTree($this->targetUserId, $this->depth);
    }

    /**
     * Migas de pan de ancestros binarios para navegación histórica.
     *
     * @return array<int, array{id: int, name: string, username: string, is_root: bool}>
     */
    #[Computed]
    public function breadcrumbs(): array
    {
        /** @var User $authUser */
        $authUser = auth()->user();
        /** @var TreeQueryService $service */
        $service = app(TreeQueryService::class);

        return $service->getBinaryBreadcrumbs($authUser, $this->targetUserId);
    }

    /**
     * Resultados de búsqueda en vivo en la organización descendente.
     *
     * @return array<int, array{id: int, name: string, username: string, depth: int}>
     */
    #[Computed]
    public function searchResults(): array
    {
        if (mb_strlen(trim($this->searchQuery)) < 2) {
            return [];
        }

        /** @var User $authUser */
        $authUser = auth()->user();
        /** @var TreeQueryService $service */
        $service = app(TreeQueryService::class);

        return $service->searchDownline($authUser, 'binary', $this->searchQuery);
    }

    /**
     * Detalles del afiliado seleccionado para el modal.
     *
     * @return array<string, mixed>|null
     */
    #[Computed]
    public function selectedUserDetails(): ?array
    {
        if (! $this->selectedUserId) {
            return null;
        }

        /** @var TreeQueryService $service */
        $service = app(TreeQueryService::class);

        return $service->getUserDetails($this->selectedUserId);
    }

    /**
     * Renderiza la vista Blade del componente.
     */
    public function render(): View
    {
        return view('livewire.office.network.binary-tree');
    }
}
