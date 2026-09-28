<?php

namespace App\Livewire\Admin\Orders;

use App\Actions\Orders\GenerateOrderPointsAction;
use App\Actions\Orders\UpdateOrderStatusAction;
use App\Models\Order;
use App\Traits\HasCrudPermissions;
use Exception;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Gestión de Pedidos')]
class OrderIndex extends Component
{
    use HasCrudPermissions;
    use WithoutUrlPagination, WithPagination;

    public string $search = '';

    /**
     * @var array<int, string>
     */
    public array $searchTerms = [];

    public string $statusFilter = 'all';

    public string $shippingFilter = 'all';

    public string $dateFrom = '';

    public string $dateTo = '';

    protected function permissionModule(): string
    {
        return 'orders';
    }

    public function mount(): void
    {
        $this->authorizeView();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedShippingFilter(): void
    {
        $this->resetPage();
    }

    public function updatedDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        $this->resetPage();
    }

    public function searchEnter(): void
    {
        if (empty(trim($this->search))) {
            $this->clearSearch();
        } else {
            $this->searchTerms = array_filter(explode(' ', trim($this->search)));
            $this->resetPage();
        }
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->searchTerms = [];
        $this->resetPage();
    }

    public function updateOrderStatus(int $orderId, int $newStatus): void
    {
        $this->authorizeEdit();

        try {
            $order = Order::findOrFail($orderId);
            $action = app(UpdateOrderStatusAction::class);
            $action->execute($order, $newStatus);

            session()->flash('success', "El estado del pedido #{$order->public_order_number} fue actualizado exitosamente.");
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function generatePoints(int $orderId): void
    {
        $this->ensurePermission('orders.points');

        try {
            $order = Order::findOrFail($orderId);
            $action = app(GenerateOrderPointsAction::class);
            $result = $action->execute($order);

            $msg = "¡Puntos generados exitosamente! Se otorgaron {$result['points_distributed']} pts personales, se beneficiaron {$result['binary_ancestors_count']} ancestros en el binario y {$result['unilevel_ancestors_count']} en unilevel.";
            if ($result['is_activated']) {
                $msg .= ' El distribuidor ha sido activado por 30 días.';
            }

            session()->flash('success', $msg);
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    /**
     * @return array<string, int>
     */
    #[Computed]
    public function counts(): array
    {
        return [
            'all' => Order::count(),
            'pending' => Order::where('status', Order::STATUS_SALE_PENDING)->count(),
            'approved' => Order::where('status', Order::STATUS_SALE_APPROVED)->count(),
            'pts_generated' => Order::where('status', Order::STATUS_PTS_GENERATED)->count(),
            'sent' => Order::where('status', Order::STATUS_SENT)->count(),
            'delivered' => Order::where('status', Order::STATUS_DELIVERED)->count(),
            'rejected' => Order::where('status', '>=', Order::STATUS_SALE_REJECTED)->count(),
        ];
    }

    public function render(): View
    {
        $query = Order::query()
            ->with(['user', 'shippingCity', 'shippingDepartment', 'shippingCountry', 'items']);

        // Filtro de Búsqueda
        if (! empty($this->searchTerms)) {
            foreach ($this->searchTerms as $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('public_order_number', 'like', '%'.$term.'%')
                        ->orWhere('shipping_name', 'like', '%'.$term.'%')
                        ->orWhere('shipping_document', 'like', '%'.$term.'%')
                        ->orWhere('shipping_phone', 'like', '%'.$term.'%')
                        ->orWhereHas('user', function ($uq) use ($term) {
                            $uq->where('name', 'like', '%'.$term.'%')
                                ->orWhere('last_name', 'like', '%'.$term.'%')
                                ->orWhere('username', 'like', '%'.$term.'%')
                                ->orWhere('email', 'like', '%'.$term.'%');
                        });
                });
            }
        }

        // Filtro por Estado
        if ($this->statusFilter !== 'all') {
            if ($this->statusFilter === 'rejected') {
                $query->where('status', '>=', Order::STATUS_SALE_REJECTED);
            } else {
                $query->where('status', (int) $this->statusFilter);
            }
        }

        // Filtro por Tipo de Envío
        if ($this->shippingFilter !== 'all') {
            $query->where('shipping_type', (int) $this->shippingFilter);
        }

        // Filtro por Rango de Fechas
        if (! empty($this->dateFrom)) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if (! empty($this->dateTo)) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        $orders = $query->latest('id')->paginate(12);

        return view('livewire.admin.orders.order-index', [
            'orders' => $orders,
        ]);
    }
}
