<x-layouts::app.header>
    <div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full bg-white dark:bg-zinc-900 rounded-3xl p-8 shadow-sm border border-zinc-200 dark:border-zinc-800 text-center space-y-6">
            <div class="w-16 h-16 rounded-full mx-auto flex items-center justify-center 
                {{ $status === 'approved' ? 'bg-primary/10 text-primary' : ($status === 'rejected' || $status === 'failed' ? 'bg-danger/10 text-danger' : 'bg-premium/10 text-premium') }}">
                @if ($status === 'approved')
                    <flux:icon.check class="size-8 stroke-2" />
                @elseif ($status === 'rejected' || $status === 'failed')
                    <flux:icon.x-mark class="size-8 stroke-2" />
                @else
                    <flux:icon.clock class="size-8 stroke-2" />
                @endif
            </div>

            <div class="space-y-2">
                <h1 class="text-2xl font-bold text-ink dark:text-zinc-100">{{ $message }}</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Estado: <strong class="uppercase text-ink dark:text-zinc-200">{{ $status }}</strong>
                </p>
                @if ($orderId)
                    <p class="text-xs font-mono text-zinc-400">Referencia de Orden: {{ $orderId }}</p>
                @endif
            </div>

            <div class="pt-4 flex flex-col sm:flex-row gap-3 justify-center">
                @if ($order)
                    <a href="{{ route('orders.show', $order) }}" class="w-full">
                        <flux:button variant="primary" class="w-full justify-center">
                            Ver Detalles del Pedido
                        </flux:button>
                    </a>
                @endif
                <a href="{{ route('products.index') }}" class="w-full">
                    <flux:button variant="ghost" class="w-full justify-center">
                        Ir a la Tienda
                    </flux:button>
                </a>
            </div>
        </div>
    </div>
</x-layouts::app.header>
