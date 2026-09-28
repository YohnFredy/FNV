<?php

namespace App\Livewire\Admin\Orders;

use App\Actions\Orders\GenerateOrderPointsAction;
use App\Actions\Orders\UpdateOrderStatusAction;
use App\Models\Order;
use App\Models\PointTransaction;
use App\Traits\HasCrudPermissions;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Detalle de Pedido')]
class OrderShow extends Component
{
    use HasCrudPermissions;

    public Order $order;

    public int $selectedStatus = 1;

    protected function permissionModule(): string
    {
        return 'orders';
    }

    public function mount(Order $order): void
    {
        $this->authorizeView();

        $this->order = $order->load([
            'user.userData',
            'user.binaryNode',
            'user.unilevelNode',
            'items.product.latestImage',
            'billingData.documentType',
            'billingData.city',
            'billingData.department',
            'billingData.country',
            'shippingCountry',
            'shippingDepartment',
            'shippingCity',
            'webhook',
        ]);

        $this->selectedStatus = $this->order->status;
    }

    public function generatePoints(): void
    {
        $this->ensurePermission('orders.points');

        try {
            $action = app(GenerateOrderPointsAction::class);
            $result = $action->execute($this->order);

            $msg = "¡Puntos generados exitosamente! Se otorgaron {$result['points_distributed']} pts personales, se beneficiaron {$result['binary_ancestors_count']} ancestros en el binario y {$result['unilevel_ancestors_count']} en unilevel.";
            if ($result['is_activated']) {
                $msg .= ' El comprador ha sido activado por 30 días en el sistema.';
            }

            $this->order->refresh();
            $this->selectedStatus = $this->order->status;
            session()->flash('success', $msg);
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function updateStatus(int $status): void
    {
        $this->authorizeEdit();

        try {
            $action = app(UpdateOrderStatusAction::class);
            $this->order = $action->execute($this->order, $status);
            $this->selectedStatus = $this->order->status;

            session()->flash('success', "El estado del pedido #{$this->order->public_order_number} fue actualizado a ".self::getStatusLabel($status).'.');
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function applyManualStatus(): void
    {
        $this->updateStatus($this->selectedStatus);
    }

    /**
     * Transacciones de puntos originadas por este pedido.
     *
     * @return Collection<int, PointTransaction>
     */
    #[Computed]
    public function pointTransactions(): Collection
    {
        return PointTransaction::where('source_type', 'order')
            ->where('order_id', $this->order->id)
            ->with('user')
            ->orderBy('id', 'asc')
            ->get();
    }

    public static function getStatusLabel(int $status): string
    {
        return match ($status) {
            Order::STATUS_SALE_PENDING => 'Pendiente de Pago',
            Order::STATUS_SALE_APPROVED => 'Pago Aprobado',
            Order::STATUS_PTS_GENERATED => 'Puntos Generados en Red',
            Order::STATUS_SENT => 'Enviado / En Camino',
            Order::STATUS_DELIVERED => 'Entregado al Cliente',
            Order::STATUS_SALE_REJECTED => 'Pago / Orden Rechazada',
            Order::STATUS_VOIDED => 'Anulada',
            Order::STATUS_VOID_REJECTED => 'Anulación Rechazada',
            default => "Estado {$status}",
        };
    }

    public function render(): View
    {
        return view('livewire.admin.orders.order-show');
    }
}
