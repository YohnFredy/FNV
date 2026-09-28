# Regla Estricta de Paleta de Colores y Modo Oscuro (Dark Mode)

> [!IMPORTANT]
> **REGLA OBLIGATORIA DE DISEÑO**:
> Queda terminantemente PROHIBIDO utilizar colores fuera de la paleta oficial definida a continuación. No se deben inventar colores (como azules genéricos, índigos, morados, esmeraldas o amarillos arbitrarios).
> Todas las interfaces deben ceñirse **estrictamente** a esta paleta de tokens y escalas.

---

## 1. Paleta de Colores Oficial

Los únicos colores de marca y acento permitidos son:
- **`--color-primary` (`#0761b0`)**: Color primario institucional (acciones principales, botones clave, enlaces activos, branding). Clases Tailwind: `text-primary`, `bg-primary`, `border-primary`, etc.
- **`--color-secondary` (`#0982eb`)**: Color secundario (hover, degradados corporativos, resaltados secundarios). Clases Tailwind: `text-secondary`, `bg-secondary`, `border-secondary`, etc.
- **`--color-danger` (`#b00f07`)**: Acciones destructivas, errores, alertas críticas, cancelaciones. Clases Tailwind: `text-danger`, `bg-danger`, `border-danger`, etc.
- **`--color-premium` (`#b06f07`)**: Administración, rangos VIP, badges especiales, acentos dorados. Clases Tailwind: `text-premium`, `bg-premium`, `border-premium`, etc.
- **`--color-ink` (`#032441`)**: Azul profundo para textos de alto contraste, encabezados y fondos oscuros corporativos. Clases Tailwind: `text-ink`, `bg-ink`, `border-ink`, etc.

*Nota*: Se permite y fomenta el uso de opacidades con estos colores (ej. `bg-primary/10`, `border-primary/20`, `bg-premium/15`, etc.).

---

## 2. Regla Estricta para Modo Oscuro (Dark Mode)

En modo oscuro (`dark:`), los fondos, bordes, superficies y neutros deben utilizar **ÚNICAMENTE** la escala de grises Zinc definida:
- `--color-zinc-50`: `#fafafa`
- `--color-zinc-100`: `#f5f5f5`
- `--color-zinc-200`: `#e5e5e5`
- `--color-zinc-300`: `#d4d4d4`
- `--color-zinc-400`: `#a3a3a3`
- `--color-zinc-500`: `#737373`
- `--color-zinc-600`: `#525252`
- `--color-zinc-700`: `#404040`
- `--color-zinc-800`: `#262626`
- `--color-zinc-900`: `#171717`
- `--color-zinc-950`: `#0a0a0a`

**Restricción del modo dark**:
- No utilizar tonalidades azules, púrpuras ni grises alternativos en fondos oscuros.
- Los fondos de pantalla deben ser `dark:bg-zinc-950` o `dark:bg-zinc-900`.
- Las tarjetas y contenedores deben ser `dark:bg-zinc-900` o `dark:bg-zinc-800`.
- Los bordes deben ser `dark:border-zinc-800` o `dark:border-zinc-700`.
- Los textos deben ser predominantemente de la escala zinc: `dark:text-zinc-50`, `dark:text-zinc-100`, `dark:text-zinc-300` o `dark:text-zinc-400`.
- **Criterio estricto sobre acentos en modo oscuro**:
  - El modo oscuro debe sentirse **verdaderamente dark, sobrio y monocromático**, dominado por la escala Zinc.
  - **NO** saturar la pantalla con colores ni usar bloques llamativos en dark mode.
  - El uso de la paleta oficial (`primary`, `secondary`, `premium`, `danger`) en modo oscuro está restringido a **micro-acentos estrictamente necesarios** (por ejemplo: un indicador de estado activo, un badge puntual o una alerta crítica).
  - Cuando se use un acento en modo oscuro, debe ser discreto y atenuado (preferir opacidades bajas como `bg-primary/10`, `border-primary/20` o textos sutiles) para **nunca perder la esencia ni la elegancia del modo dark**.

---

## 3. Regla Obligatoria de Sombras y Relieve (Shadows)

> [!IMPORTANT]
> **SOMBRAS FIRMES, OSCURAS Y EQUILIBRADAS (`shadow-md shadow-ink/70`)**:
> - **Prohibidas las sombras pálidas o imperceptibles**: Nunca usar `shadow-xs`, `shadow-sm` ni sombras descoloridas que hagan ver las tarjetas y paneles planos o débiles.
> - **Modo claro**: Utilizar **`shadow-md shadow-ink/70`** en tarjetas, paneles, formularios y contenedores clave. En estados hover e interactivos usar **`hover:shadow-xl hover:shadow-ink/80`**. Esto brinda un contraste nítido, cuerpo, profundidad y relieve equilibrado sin saturar la vista.
> - **Modo oscuro**: En `dark:`, las sombras proyectadas en `ink` se cancelan o mitigan usando `dark:shadow-none` o `dark:shadow-zinc-950/40`, reforzando la estructura con bordes sutiles `dark:border-zinc-800` para mantener la atmósfera dark pura y monocromática.
