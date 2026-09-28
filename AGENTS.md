# Instrucciones y Reglas del Proyecto Multinivel

## Regla Estricta de Paleta de Colores y Modo Oscuro (Dark Mode)

> [!IMPORTANT]
> **REGLA PERMANENTE DE DISEÑO UI**:
> Todas las vistas, componentes Livewire, layouts y estilos deben utilizar **EXCLUSIVAMENTE** la siguiente paleta de colores definida en `resources/css/app.css`:
> - `--color-primary`: `#0761b0`
> - `--color-secondary`: `#0982eb`
> - `--color-danger`: `#b00f07`
> - `--color-premium`: `#b06f07`
> - `--color-ink`: `#032441`
>
> **En Modo Oscuro (Dark Mode)**:
> Se debe utilizar **ÚNICAMENTE** la escala `zinc` con sus opacidades correspondientes:
> - `--color-zinc-50`: `#fafafa`
> - `--color-zinc-100`: `#f5f5f5`
> - `--color-zinc-200`: `#e5e5e5`
> - `--color-zinc-300`: `#d4d4d4`
> - `--color-zinc-400`: `#a3a3a3`
> - `--color-zinc-500`: `#737373`
> - `--color-zinc-600`: `#525252`
> - `--color-zinc-700`: `#404040`
> - `--color-zinc-800`: `#262626`
> - `--color-zinc-900`: `#171717`
> - `--color-zinc-950`: `#0a0a0a`
>
> Queda prohibido el uso de colores ajenos (como índigos, morados, azules genéricos, esmeraldas o amarillos arbitrarios).
>
> **PROHIBICIÓN ESTRICTA DE VALORES INVENTADOS EN LA ESCALA ZINC**:
> Queda terminantemente prohibido inventar o usar valores intermedios inexistentes en Tailwind como `zinc-750`, `zinc-755`, `zinc-850`, etc. Únicamente existen y están permitidos los 11 escalones estándar oficiales: `50`, `100`, `200`, `300`, `400`, `500`, `600`, `700`, `800`, `900`, `950`. Si se requiere un tono intermedio o atenuado, se debe utilizar **EXCLUSIVAMENTE una opacidad válida sobre un escalón oficial** (por ejemplo: `zinc-800/80`, `zinc-700/60`, `zinc-900/50`) y NUNCA números inventados.
>
> **Criterio Estricto en Modo Oscuro (True Dark Monocromático)**:
> - El modo oscuro debe ser sobrio, elegante y dominado por la escala Zinc, sin saturación ni parches de color.
> - Los colores de la paleta (`primary`, `secondary`, `premium`, `danger`) en dark mode solo se usan si es estrictamente necesario, en forma de **micro-acentos sutiles** (bajas opacidades o textos puntuales) para **nunca romper la atmósfera dark pura**.

## Regla de Sombras (Shadows) y Profundidad

> [!IMPORTANT]
> **REGLA DE SOMBRAS FIRMES Y EQUILIBRADAS (`shadow-md shadow-ink/70`)**:
> - **Evitar sombras pálidas o imperceptibles** (como `shadow-xs`, `shadow-sm` o sombras con opacidades excesivamente bajas) que hagan ver la interfaz descolorida, plana o sin contraste.
> - En modo claro se debe utilizar una sombra con presencia real, oscura y bien definida utilizando el color de la paleta tinta: **`shadow-md shadow-ink/70`** (y en hover o estados elevados **`hover:shadow-xl hover:shadow-ink/80`**), logrando una sombra fuerte, visible y limpia pero sin saturar ni verse tosca.
> - En modo oscuro (`dark:`), las sombras proyectadas en `ink` se reemplazan o mitigan usando `dark:shadow-none` o `dark:shadow-zinc-950/40` con bordes sutiles `dark:border-zinc-800` para mantener la sobriedad dark monocromática.

