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
 * Componente Livewire 4 Full-Page para la Visualización del Árbol Escalonado (Unilevel).
 *
 * Características implementadas:
 * - Aislamiento perimetral: Protección estricta de descendencia con la Closure Table `unilevel_paths`.
 * - Selector de profundidad dinámica de 2 a 5 generaciones.
 * - Modo Dual: Vista de Lienzo Gráfico Interactivo (con paneo/zoom) o Vista de Lista Jerárquica Expandible (Acordeón).
 * - Búsqueda de patrocinados en tiempo real.
 * - Navegación descendente (drill-down) y ascendente con migas de pan.
 * - Métricas en O(1) de patrocinados frontales y tamaño total de la red unilevel.
 */
#[Layout('layouts.app')]
#[Title('Árbol Unilevel')]
class UnilevelTree extends Component
{
    /**
     * ID del usuario cuya red unilevel se está visualizando como raíz.
     */
    #[Url(as: 'user', history: true)]
    public int $targetUserId = 0;

    /**
     * Cantidad de niveles de profundidad genealógica (2 a 5 generaciones).
     */
    #[Url(as: 'depth')]
    public int $depth = 3;

    /**
     * Modo de visualización: 'graph' (lienzo interactivo) o 'list' (jerárquico en acordeón).
     */
    #[Url(as: 'mode')]
    public string $viewMode = 'graph';

    /**
     * Consulta para búsqueda rápida en tiempo real.
     */
    public string $searchQuery = '';

    /**
     * ID del usuario seleccionado para ver su ficha técnica modal.
     */
    public ?int $selectedUserId = null;

    /**
     * Nodos expandidos en el modo lista jerárquica [userId => true].
     *
     * @var array<int, bool>
     */
    public array $expandedNodes = [];

    /**
     * Mensaje de aviso perimetral de seguridad.
     */
    public ?string $statusMessage = null;

    /**
     * Inicialización del componente.
     */
    public function mount(): void
    {
        /** @var User $authUser */
        $authUser = auth()->user();

        if ($this->depth < 2 || $this->depth > 5) {
            $this->depth = 3;
        }

        if (! in_array($this->viewMode, ['graph', 'list'], true)) {
            $this->viewMode = 'graph';
        }

        if ($this->targetUserId <= 0) {
            $this->targetUserId = $authUser->id;
        } else {
            /** @var TreeQueryService $service */
            $service = app(TreeQueryService::class);
            if (! $service->canAccessUnilevelUser($authUser, $this->targetUserId)) {
                $this->statusMessage = __('No tienes permisos para consultar la red unilevel de este afiliado.');
                $this->targetUserId = $authUser->id;
            }
        }

        // Auto-expandir la raíz en modo lista
        $this->expandedNodes[$this->targetUserId] = true;
    }

    /**
     * Cambia entre modo gráfico ('graph') y modo lista expandible ('list').
     */
    public function setViewMode(string $mode): void
    {
        if (in_array($mode, ['graph', 'list'], true)) {
            $this->viewMode = $mode;
        }
    }

    /**
     * Ajusta la profundidad genealógica a renderizar (de 2 a 5 generaciones).
     */
    public function setDepth(int $depth): void
    {
        if ($depth >= 2 && $depth <= 5) {
            $this->depth = $depth;
        }
    }

    /**
     * Enfoca la red unilevel en un afiliado descendiente específico.
     */
    public function focusNode(int $userId): void
    {
        /** @var User $authUser */
        $authUser = auth()->user();
        /** @var TreeQueryService $service */
        $service = app(TreeQueryService::class);

        if ($service->canAccessUnilevelUser($authUser, $userId)) {
            $this->targetUserId = $userId;
            $this->searchQuery = '';
            $this->selectedUserId = null;
            $this->statusMessage = null;
            $this->expandedNodes = [$userId => true];
        } else {
            $this->statusMessage = __('Acceso denegado: El afiliado no pertenece a tu red unilevel.');
        }
    }

    /**
     * Sube un nivel hacia el patrocinador directo.
     */
    public function goUpOneLevel(): void
    {
        /** @var User $authUser */
        $authUser = auth()->user();

        if ($this->targetUserId === $authUser->id) {
            return;
        }

        /** @var TreeQueryService $service */
        $service = app(TreeQueryService::class);
        $sponsorId = $service->getUnilevelSponsorId($this->targetUserId);

        if ($sponsorId && $service->canAccessUnilevelUser($authUser, $sponsorId)) {
            $this->targetUserId = $sponsorId;
            $this->selectedUserId = null;
            $this->expandedNodes[$sponsorId] = true;
        } else {
            $this->targetUserId = $authUser->id;
        }
    }

    /**
     * Restablece la vista a la raíz personal del usuario autenticado.
     */
    public function resetToMyTree(): void
    {
        $this->targetUserId = (int) auth()->id();
        $this->selectedUserId = null;
        $this->searchQuery = '';
        $this->statusMessage = null;
        $this->expandedNodes = [$this->targetUserId => true];
    }

    /**
     * Expande o colapsa un nodo en la vista de lista jerárquica.
     */
    public function toggleNode(int $userId): void
    {
        if (isset($this->expandedNodes[$userId])) {
            unset($this->expandedNodes[$userId]);
        } else {
            $this->expandedNodes[$userId] = true;
        }
    }

    /**
     * Abre el modal de ficha técnica para un afiliado.
     */
    public function openUserDetails(int $userId): void
    {
        /** @var User $authUser */
        $authUser = auth()->user();
        /** @var TreeQueryService $service */
        $service = app(TreeQueryService::class);

        if ($service->canAccessUnilevelUser($authUser, $userId)) {
            $this->selectedUserId = $userId;
        }
    }

    /**
     * Cierra el modal de detalles técnicos.
     */
    public function closeUserDetails(): void
    {
        $this->selectedUserId = null;
    }

    /**
     * Árbol Unilevel completo en memoria optimizado para el renderizado.
     *
     * @return array<string, mixed>|null
     */
    #[Computed]
    public function tree(): ?array
    {
        /** @var TreeQueryService $service */
        $service = app(TreeQueryService::class);

        return $service->getUnilevelTree($this->targetUserId, $this->depth);
    }

    /**
     * Migas de pan de la línea de patrocinio.
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

        return $service->getUnilevelBreadcrumbs($authUser, $this->targetUserId);
    }

    /**
     * Resultados de búsqueda en la red unilevel del usuario.
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

        return $service->searchDownline($authUser, 'unilevel', $this->searchQuery);
    }

    /**
     * Ficha técnica del afiliado seleccionado.
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
        return view('livewire.office.network.unilevel-tree');
    }
}
