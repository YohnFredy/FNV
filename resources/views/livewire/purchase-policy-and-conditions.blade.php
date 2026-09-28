<div>
    <p class="text-xs sm:text-sm text-ink/80 dark:text-zinc-300 font-medium">
        {{ __('He leído y acepto la') }}
        <button
            type="button"
            wire:click="policy"
            class="font-bold text-primary hover:text-secondary underline decoration-primary/40 hover:decoration-secondary cursor-pointer transition-colors inline-block text-left">
            {{ __('Política de Términos y Condiciones de Compra') }}
        </button>
    </p>

    <!-- Modal con los 17 Términos y Condiciones -->
    <flux:modal wire:model="terms" class="w-full max-w-3xl !p-6 sm:!p-8 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 shadow-2xl shadow-ink/70 dark:shadow-none">
        <div class="space-y-6">
            <!-- Header Modal -->
            <div class="flex items-start gap-3 pb-4 border-b border-zinc-200/80 dark:border-zinc-800">
                <div class="p-2.5 rounded-2xl bg-primary/10 text-primary dark:bg-zinc-800 dark:text-zinc-200 shrink-0">
                    <flux:icon.shield-check class="size-6 text-primary" />
                </div>
                <div>
                    <h3 class="text-lg sm:text-xl font-extrabold text-ink dark:text-zinc-100 tracking-tight">
                        {{ __('Política de Términos y Condiciones de Compra') }}
                    </h3>
                    <p class="text-xs text-ink/60 dark:text-zinc-400 mt-0.5">
                        {{ __('Vigente y aplicable a todas las transacciones realizadas en nuestra tienda en línea.') }}
                    </p>
                </div>
            </div>

            <!-- Contenido Escroleable con los 17 Puntos Oficiales -->
            <div class="max-h-[60vh] overflow-y-auto pr-3 space-y-4 text-sm text-ink/85 dark:text-zinc-300 leading-relaxed">
                <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-950/60 border border-zinc-200/80 dark:border-zinc-800 text-xs sm:text-sm text-ink/80 dark:text-zinc-300">
                    {{ __('Al realizar una compra en nuestra tienda en línea, el cliente acepta los siguientes términos y condiciones, aplicables a todos los productos, incluidos los productos naturales y otros ofrecidos en nuestro sitio web.') }}
                </div>

                <div class="space-y-3.5">
                    <!-- 1 -->
                    <div class="p-3.5 rounded-xl border border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 space-y-1.5">
                        <h4 class="font-bold text-ink dark:text-zinc-100 text-sm flex items-center gap-2">
                            <span class="size-5 rounded-full bg-primary/10 text-primary text-xs font-black flex items-center justify-center">1</span>
                            {{ __('Descripción de Productos') }}
                        </h4>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('Todos los productos disponibles en nuestra tienda están debidamente descritos, incluyendo sus características, especificaciones, usos sugeridos y, en el caso de productos naturales, cualquier contraindicaciones. Es responsabilidad del cliente revisar esta información antes de completar la compra.') }}
                        </p>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7 font-medium">
                            {{ __('Los productos naturales no sustituyen un tratamiento médico. Recomendamos consultar con un profesional de la salud antes de utilizar cualquier producto natural, especialmente si se tiene una condición médica preexistente.') }}
                        </p>
                    </div>

                    <!-- 2 -->
                    <div class="p-3.5 rounded-xl border border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 space-y-1.5">
                        <h4 class="font-bold text-ink dark:text-zinc-100 text-sm flex items-center gap-2">
                            <span class="size-5 rounded-full bg-primary/10 text-primary text-xs font-black flex items-center justify-center">2</span>
                            {{ __('Política de Envío y Entrega') }}
                        </h4>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('Realizamos envíos a nivel nacional dentro de Colombia. El tiempo estimado de entrega depende de la ubicación del cliente y será especificado durante el proceso de compra.') }}
                        </p>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7 font-medium text-primary">
                            {{ __('El cliente debe pagar el valor del producto al momento de realizar la compra en nuestra tienda en línea. El valor del envío no está incluido en el pago inicial y deberá ser pagado por el cliente al recibir el producto directamente al transportador ("Pago del envío contra entrega").') }}
                        </p>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('Si el cliente no se encuentra disponible para recibir el pedido, el transportista puede reprogramar la entrega o, en algunos casos, devolver el pedido a nuestra bodega. El cliente deberá asumir cualquier costo adicional que esto conlleve.') }}
                        </p>
                    </div>

                    <!-- 3 -->
                    <div class="p-3.5 rounded-xl border border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 space-y-1.5">
                        <h4 class="font-bold text-ink dark:text-zinc-100 text-sm flex items-center gap-2">
                            <span class="size-5 rounded-full bg-primary/10 text-primary text-xs font-black flex items-center justify-center">3</span>
                            {{ __('Política de Devoluciones y Reembolsos') }}
                        </h4>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('Se aceptarán devoluciones solo en caso de que el producto llegue en mal estado, esté defectuoso o no corresponda con el pedido realizado. El cliente debe notificar cualquier irregularidad en un plazo de 3 días hábiles posteriores a la recepción del producto.') }}
                        </p>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('Para proceder con una devolución, el producto debe estar sin uso, en su empaque original y con todos los accesorios incluidos.') }}
                        </p>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('Una vez aprobada la devolución, el reembolso del valor pagado por el producto se procesará en un plazo de 15 días hábiles. Los costos de envío no son reembolsables.') }}
                        </p>
                    </div>

                    <!-- 4 -->
                    <div class="p-3.5 rounded-xl border border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 space-y-1.5">
                        <h4 class="font-bold text-ink dark:text-zinc-100 text-sm flex items-center gap-2">
                            <span class="size-5 rounded-full bg-primary/10 text-primary text-xs font-black flex items-center justify-center">4</span>
                            {{ __('Política de Cancelación de Pedido') }}
                        </h4>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('Los pedidos pueden ser cancelados sin costo alguno siempre que el producto no haya sido despachado. Si el producto ya ha sido enviado, el cliente deberá asumir los costos de devolución.') }}
                        </p>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('Los productos personalizados o elaborados bajo pedido no pueden ser cancelados una vez iniciada su producción.') }}
                        </p>
                    </div>

                    <!-- 5 -->
                    <div class="p-3.5 rounded-xl border border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 space-y-1.5">
                        <h4 class="font-bold text-ink dark:text-zinc-100 text-sm flex items-center gap-2">
                            <span class="size-5 rounded-full bg-primary/10 text-primary text-xs font-black flex items-center justify-center">5</span>
                            {{ __('Política de Privacidad y Protección de Datos') }}
                        </h4>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('Los datos personales proporcionados durante el proceso de compra serán tratados de acuerdo con la legislación vigente en Colombia (Ley 1581 de 2012). Utilizaremos la información solo para procesar el pedido, realizar el envío y, con el consentimiento del cliente, enviar comunicaciones promocionales.') }}
                        </p>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('Los datos del cliente no serán compartidos con terceros salvo en los casos necesarios para completar el proceso de entrega o cuando la ley lo requiera.') }}
                        </p>
                    </div>

                    <!-- 6 -->
                    <div class="p-3.5 rounded-xl border border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 space-y-1.5">
                        <h4 class="font-bold text-ink dark:text-zinc-100 text-sm flex items-center gap-2">
                            <span class="size-5 rounded-full bg-primary/10 text-primary text-xs font-black flex items-center justify-center">6</span>
                            {{ __('Pago y Facturación') }}
                        </h4>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('El cliente debe pagar el valor del producto al momento de realizar la compra a través de los métodos de pago disponibles en nuestra tienda en línea.') }}
                        </p>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7 font-medium text-primary">
                            {{ __('El valor del envío no está incluido en el pago inicial y deberá ser abonado directamente al transportador al momento de recibir el producto.') }}
                        </p>
                    </div>

                    <!-- 7 -->
                    <div class="p-3.5 rounded-xl border border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 space-y-1.5">
                        <h4 class="font-bold text-ink dark:text-zinc-100 text-sm flex items-center gap-2">
                            <span class="size-5 rounded-full bg-primary/10 text-primary text-xs font-black flex items-center justify-center">7</span>
                            {{ __('Responsabilidad del Cliente') }}
                        </h4>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('El cliente es responsable de utilizar los productos de manera adecuada y conforme a las instrucciones proporcionadas.') }}
                        </p>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('En el caso de productos naturales, no nos hacemos responsables por cualquier efecto adverso causado por el uso incorrecto o excesivo de los mismos. Se recomienda consultar a un profesional antes de su uso, especialmente si se combinan con otros productos o tratamientos.') }}
                        </p>
                    </div>

                    <!-- 8 -->
                    <div class="p-3.5 rounded-xl border border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 space-y-1.5">
                        <h4 class="font-bold text-ink dark:text-zinc-100 text-sm flex items-center gap-2">
                            <span class="size-5 rounded-full bg-primary/10 text-primary text-xs font-black flex items-center justify-center">8</span>
                            {{ __('Exoneración de Responsabilidad') }}
                        </h4>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('No seremos responsables de daños o pérdidas indirectas, incluyendo, pero no limitándose a, reacciones adversas a los productos naturales, pérdida de ingresos, o cualquier otro daño relacionado con el uso de los productos adquiridos en nuestra tienda.') }}
                        </p>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('La responsabilidad por cualquier producto defectuoso se limita al valor del producto adquirido.') }}
                        </p>
                    </div>

                    <!-- 9 -->
                    <div class="p-3.5 rounded-xl border border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 space-y-1.5">
                        <h4 class="font-bold text-ink dark:text-zinc-100 text-sm flex items-center gap-2">
                            <span class="size-5 rounded-full bg-primary/10 text-primary text-xs font-black flex items-center justify-center">9</span>
                            {{ __('Jurisdicción y Ley Aplicable') }}
                        </h4>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('Estas políticas están regidas por las leyes colombianas. Cualquier disputa que surja en relación con la compra de productos a través de nuestra tienda en línea será resuelta ante los tribunales competentes de Colombia.') }}
                        </p>
                    </div>

                    <!-- 10 -->
                    <div class="p-3.5 rounded-xl border border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 space-y-1.5">
                        <h4 class="font-bold text-ink dark:text-zinc-100 text-sm flex items-center gap-2">
                            <span class="size-5 rounded-full bg-primary/10 text-primary text-xs font-black flex items-center justify-center">10</span>
                            {{ __('Política de Garantía') }}
                        </h4>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('Todos los productos vendidos en nuestra tienda tienen una garantía contada a partir de la fecha de recepción del producto. La garantía cubre defectos de fabricación y fallas del producto.') }}
                        </p>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('Quedan excluidos de la garantía los productos que hayan sido alterados, modificados o utilizados de manera incorrecta.') }}
                        </p>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('Para reclamar la garantía, el cliente debe proporcionar la factura de compra y fotografías del defecto o problema, y deberá enviarnos el producto para su evaluación.') }}
                        </p>
                    </div>

                    <!-- 11 -->
                    <div class="p-3.5 rounded-xl border border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 space-y-1.5">
                        <h4 class="font-bold text-ink dark:text-zinc-100 text-sm flex items-center gap-2">
                            <span class="size-5 rounded-full bg-primary/10 text-primary text-xs font-black flex items-center justify-center">11</span>
                            {{ __('Limitación de Responsabilidad por Entregas') }}
                        </h4>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('Si bien haremos todos los esfuerzos posibles para asegurar que los productos lleguen dentro del tiempo de entrega estimado, no nos hacemos responsables por retrasos ocasionados por eventos fuera de nuestro control, como problemas logísticos con los transportistas, desastres naturales, huelgas, o medidas gubernamentales.') }}
                        </p>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('En caso de que un pedido se retrase considerablemente, nos comprometemos a mantener una comunicación continua con el cliente para informarle sobre el estado de su pedido.') }}
                        </p>
                    </div>

                    <!-- 12 -->
                    <div class="p-3.5 rounded-xl border border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 space-y-1.5">
                        <h4 class="font-bold text-ink dark:text-zinc-100 text-sm flex items-center gap-2">
                            <span class="size-5 rounded-full bg-primary/10 text-primary text-xs font-black flex items-center justify-center">12</span>
                            {{ __('Modificación de los Términos y Condiciones') }}
                        </h4>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('Nos reservamos el derecho de modificar estos Términos y Condiciones en cualquier momento. Los cambios serán efectivos una vez publicados en nuestra página web.') }}
                        </p>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('El cliente será notificado sobre cambios significativos que puedan afectar su compra actual o futura, a través de los medios de contacto proporcionados.') }}
                        </p>
                    </div>

                    <!-- 13 -->
                    <div class="p-3.5 rounded-xl border border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 space-y-1.5">
                        <h4 class="font-bold text-ink dark:text-zinc-100 text-sm flex items-center gap-2">
                            <span class="size-5 rounded-full bg-primary/10 text-primary text-xs font-black flex items-center justify-center">13</span>
                            {{ __('Disponibilidad de los Productos') }}
                        </h4>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('Todos los productos en nuestra tienda están sujetos a disponibilidad. En el caso de que un producto comprado no esté disponible por cualquier motivo (incluyendo falta de stock), nos pondremos en contacto con el cliente para ofrecer una alternativa o realizar el reembolso completo del monto pagado.') }}
                        </p>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('Nos reservamos el derecho de retirar o modificar cualquier producto de nuestro sitio web sin previo aviso.') }}
                        </p>
                    </div>

                    <!-- 14 -->
                    <div class="p-3.5 rounded-xl border border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 space-y-1.5">
                        <h4 class="font-bold text-ink dark:text-zinc-100 text-sm flex items-center gap-2">
                            <span class="size-5 rounded-full bg-primary/10 text-primary text-xs font-black flex items-center justify-center">14</span>
                            {{ __('Promociones y Ofertas') }}
                        </h4>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('Las promociones, descuentos y ofertas están sujetas a términos específicos que serán comunicados en el momento de su publicación. Estos términos pueden incluir restricciones en cuanto a fechas, productos participantes, cantidades limitadas, etc.') }}
                        </p>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('Los cupones o códigos promocionales no son acumulables, a menos que se especifique lo contrario. Cada promoción está sujeta a un uso por cliente.') }}
                        </p>
                    </div>

                    <!-- 15 -->
                    <div class="p-3.5 rounded-xl border border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 space-y-1.5">
                        <h4 class="font-bold text-ink dark:text-zinc-100 text-sm flex items-center gap-2">
                            <span class="size-5 rounded-full bg-primary/10 text-primary text-xs font-black flex items-center justify-center">15</span>
                            {{ __('Fuerza Mayor') }}
                        </h4>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('No seremos responsables por cualquier incumplimiento de nuestras obligaciones si este incumplimiento se debe a eventos fuera de nuestro control razonable, como desastres naturales, conflictos laborales, pandemias, fallos en sistemas de transporte, entre otros.') }}
                        </p>
                    </div>

                    <!-- 16 -->
                    <div class="p-3.5 rounded-xl border border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 space-y-1.5">
                        <h4 class="font-bold text-ink dark:text-zinc-100 text-sm flex items-center gap-2">
                            <span class="size-5 rounded-full bg-primary/10 text-primary text-xs font-black flex items-center justify-center">16</span>
                            {{ __('Uso de Productos') }}
                        </h4>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('Todos los productos vendidos en nuestra tienda están destinados para el uso indicado en su descripción. No nos hacemos responsables por daños o efectos adversos que puedan surgir del uso inadecuado de los productos, tanto naturales como no naturales.') }}
                        </p>
                        <p class="text-xs text-ink/75 dark:text-zinc-400 pl-7">
                            {{ __('En el caso de productos naturales, el cliente es responsable de verificar si tiene alergias o intolerancias a alguno de los ingredientes del producto antes de su uso.') }}
                        </p>
                    </div>

                    <!-- 17 -->
                    <div class="p-4 rounded-xl border border-primary/30 bg-primary/5 space-y-1.5 shadow-xs">
                        <h4 class="font-bold text-primary text-sm flex items-center gap-2">
                            <span class="size-5 rounded-full bg-primary text-white text-xs font-black flex items-center justify-center">17</span>
                            {{ __('Aceptación de los Términos') }}
                        </h4>
                        <p class="text-xs text-ink/90 dark:text-zinc-200 pl-7 font-semibold">
                            {{ __('Al completar una compra, el cliente acepta estos términos y condiciones, y reconoce que ha sido informado sobre las políticas de entrega, devoluciones, y las características del producto adquirido.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer con Botón Cerrar -->
            <div class="flex items-center justify-end pt-4 border-t border-zinc-200/80 dark:border-zinc-800">
                <flux:button variant="primary" wire:click="$set('terms', false)" class="!bg-primary hover:!bg-secondary text-white! font-bold !py-2.5 px-6 rounded-xl cursor-pointer">
                    {{ __('Entendido y Aceptar') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
