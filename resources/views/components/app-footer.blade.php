<footer class="bg-ink dark:bg-zinc-950 text-white border-t border-zinc-800 pt-16 pb-12 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">

            <!-- Columna 1: Presentación & Redes -->
            <div>
                <a href="{{ route('home') }}" class="inline-block text-2xl font-extrabold tracking-tight text-white mb-4 hover:opacity-90 transition-opacity" wire:navigate>
                    fornuvi
                </a>
                <p class="text-zinc-400 text-sm leading-relaxed mb-6">
                    Una compañía en el sector de salud y bienestar que ofrece múltiples oportunidades financieras a través de un sistema global de asociación.
                </p>

                <!-- Redes Sociales -->
                <div class="flex items-center gap-3">
                    <a href="#" class="size-9 rounded-lg bg-zinc-800/80 dark:bg-zinc-900 flex items-center justify-center text-zinc-300 hover:text-white hover:bg-primary transition-all" title="Facebook">
                        <svg class="size-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="#" class="size-9 rounded-lg bg-zinc-800/80 dark:bg-zinc-900 flex items-center justify-center text-zinc-300 hover:text-white hover:bg-primary transition-all" title="Instagram">
                        <svg class="size-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    <a href="#" class="size-9 rounded-lg bg-zinc-800/80 dark:bg-zinc-900 flex items-center justify-center text-zinc-300 hover:text-white hover:bg-primary transition-all" title="X / Twitter">
                        <svg class="size-4 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="#" class="size-9 rounded-lg bg-zinc-800/80 dark:bg-zinc-900 flex items-center justify-center text-zinc-300 hover:text-white hover:bg-primary transition-all" title="LinkedIn">
                        <svg class="size-4 fill-current" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                    </a>
                </div>
            </div>

            <!-- Columna 2: Enlaces Rápidos -->
            <div>
                <h3 class="text-base font-bold text-white uppercase tracking-wider mb-5">
                    Enlaces rápidos
                </h3>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('home') }}" class="text-zinc-300 hover:text-white transition-colors" wire:navigate>Inicio</a></li>
                    <li><a href="{{ route('products.index') }}" class="text-zinc-300 hover:text-white transition-colors" wire:navigate>Tienda / Productos</a></li>
                    <li><a href="{{ route('office.dashboard') }}" class="text-zinc-300 hover:text-white transition-colors" wire:navigate>Oficina Virtual</a></li>
                    <li><a href="{{ url('/#about') }}" class="text-zinc-300 hover:text-white transition-colors">Nosotros</a></li>
                    <li><a href="{{ url('/#ingresos') }}" class="text-zinc-300 hover:text-white transition-colors">Plan de pagos</a></li>
                </ul>
            </div>

            <!-- Columna 3: Soporte -->
            <div>
                <h3 class="text-base font-bold text-white uppercase tracking-wider mb-5">
                    Soporte
                </h3>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ url('/#faq') }}" class="text-zinc-400 hover:text-white transition-colors">Preguntas frecuentes</a></li>
                    <li><a href="{{ route('legal.terms') }}" class="text-zinc-400 hover:text-white transition-colors" wire:navigate>Términos y condiciones</a></li>
                    <li><a href="{{ route('legal.contract') }}" class="text-zinc-400 hover:text-white transition-colors" wire:navigate>Contrato de afiliación</a></li>
                    <li><a href="{{ route('legal.terms') }}" class="text-zinc-400 hover:text-white transition-colors" wire:navigate>Política de privacidad</a></li>
                    <li><a href="{{ url('/#faq') }}" class="text-zinc-400 hover:text-white transition-colors">Centro de ayuda</a></li>
                </ul>
            </div>

            <!-- Columna 4: Contacto -->
            <div>
                <h3 class="text-base font-bold text-white uppercase tracking-wider mb-5">
                    Contacto
                </h3>
                <ul class="space-y-3.5 text-sm">
                    <li class="flex items-start gap-3 text-zinc-300">
                        <flux:icon.phone class="size-5 text-primary dark:text-zinc-400 shrink-0 mt-0.5" />
                        <span>+57 (314) 520-78-14</span>
                    </li>
                    <li class="flex items-start gap-3 text-zinc-300">
                        <flux:icon.envelope class="size-5 text-primary dark:text-zinc-400 shrink-0 mt-0.5" />
                        <span>info@fornuvi.com</span>
                    </li>
                    <li class="flex items-start gap-3 text-zinc-300">
                        <flux:icon.map-pin class="size-5 text-primary dark:text-zinc-400 shrink-0 mt-0.5" />
                        <span>Latinoamérica & Cobertura Global</span>
                    </li>
                </ul>
            </div>

        </div>

        <!-- Copyright Bar -->
        <div class="border-t border-zinc-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-zinc-400">
            <p>&copy; {{ date('Y') }} Fornuvi. Todos los derechos reservados.</p>
            <div class="flex items-center gap-6">
                <a href="{{ route('legal.terms') }}" class="hover:text-white transition-colors" wire:navigate>Términos y condiciones</a>
                <a href="{{ route('legal.contract') }}" class="hover:text-white transition-colors" wire:navigate>Contrato de afiliación</a>
                <a href="{{ route('legal.terms') }}" class="hover:text-white transition-colors" wire:navigate>Política de privacidad</a>
            </div>
        </div>
    </div>
</footer>
