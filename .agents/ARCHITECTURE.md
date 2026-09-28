# Arquitectura del Equipo de Agentes de Antigravity

Este documento describe el sistema de agentes especializados y subagentes diseñados para el proyecto **Multinivel** con **Laravel 13**, **Livewire 4**, **Flux UI**, **Tailwind CSS v4**, y motores de cálculo, base de datos y visualización MLM de alta concurrencia.

```mermaid
flowchart TD
    User([Usuario / Desarrollador]) --> Director[agente-director\nOrquestador Principal & QA Architect]
    
    subgraph Frontend & Mobile UX
        Director --> FrontendUI[frontend-ui-architect\nFull-Page Livewire 4, Flux UI, Tailwind v4 & Alpine]
        Director --> MobileUX[mobile-responsive-craftsman\nEspecialista Mobile-First 320-430px & Touch UI]
        Director --> MLMVisual[mlm-tree-visualizer\nÁrbol Genealógico Dinámico, Canvas/SVG & Panning]
    end

    subgraph Backend Core, Security & MLM Logic
        Director --> BackendCraftsman[laravel-backend-craftsman\nServicios, Action Classes, DTOs & Events]
        Director --> SecurityArchitect[spatie-security-architect\nSpatie Roles, Permissions, Policies & Super Admin Gate]
        Director --> MLMLogic[mlm-logic-engine\nMotor Matemático Binario & Escalonado]
    end

    subgraph Persistencia, Escalabilidad & Alta Concurrencia
        Director --> DatabaseDBA[database-performance-dba\nDBA, Closure Tables, Índices Masivos & Migraciones]
        Director --> HighPerf[high-concurrency-architect\nMillones de TXs, Redis, Queues, Locks & Idempotencia]
    end

    subgraph Integración de Contexto
        FrontendUI -.-> LaravelBoost[Laravel Boost MCP]
        BackendCraftsman -.-> LaravelBoost
        SecurityArchitect -.-> LaravelBoost
        DatabaseDBA -.-> LaravelBoost
        Director -.-> LaravelBoost
    end
```

---

## 👥 Roles del Equipo de Agentes

### 1. `agente-director` (Orquestador Principal y QA)
- **Rol:** Supervisor y Arquitecto general. Coordina los flujos, desglosa requerimientos en subtareas, delega a los subagentes adecuados y asegura que todo pase las pruebas (`pint`, `phpstan`, `phpunit`) antes de aprobar.

### 2. `spatie-security-architect` (Especialista en Seguridad, Roles y Permisos con Spatie)
- **Rol:** Diseña, implementa y audita el control de acceso basado en roles y permisos (RBAC) con `spatie/laravel-permission`. Configura Enums respaldados por String en PHP 8.3+, seeders 100% idempotentes, interceptor Super Admin con `Gate::before`, Policies desacopladas de Laravel, protección multicapa en Livewire 4 / Blade, optimización con caché en Redis y prevención de escalada de privilegios.

### 3. `frontend-ui-architect` (Experto en Frontend Moderno: Livewire 4, Flux, Tailwind v4 & Alpine)
- **Rol:** Diseña y programa componentes Full-Page (`#[Layout]`), directivas de Livewire 4 (`wire:model.live`, `#[Computed]`, `#[Lazy]`, `wire:navigate`), componentes Flux UI (`<flux:* />`), Alpine.js para interactividad local instantánea (cero latencia de red), Tailwind CSS v4, micro-animaciones y diseño visual de clase mundial. Conectado a **Laravel Boost**.

### 4. `mobile-responsive-craftsman` (Auditor & Especialista Mobile 320px - 430px)
- **Rol:** Garantiza que cada pantalla, tabla, modal, formulario y árbol multinivel funcione impecablemente en celulares. Evita desbordamientos horizontales, solapamiento de textos y diseña interfaces 'touch-first' y bottom-sheets.

### 5. `laravel-backend-craftsman` (Arquitecto de Backend Laravel 13)
- **Rol:** Construye la capa de negocio desacoplada: Servicios, Clases de Acción (Action Classes), DTOs con tipado estricto (PHP 8.3+), Form Requests, Eventos/Listeners y Políticas de autorización. Mantiene los controladores y componentes Livewire completamente limpios.

### 6. `database-performance-dba` (DBA & Especialista en Persistencia)
- **Rol:** Diseña migraciones, esquemas para árboles jerárquicos (Closure Tables, Materialized Paths), índices compuestos de alto rendimiento, optimización de queries para eliminar el problema N+1, `cursorPaginate()` y tablas de libro contable inmutable para auditoría financiera.

### 7. `mlm-logic-engine` (Motor Binario y Escalonado / Unilevel)
- **Rol:** Implementa y blinda las reglas de negocio del Multinivel: colocación en red binaria (spillover / derrame, pata débil/fuerte), planes escalonados con compresión dinámica, cálculo de puntos grupales (PG) y personales (PV), rangos, calificaciones mensuales, cierres de ciclo y comisiones. Cero flotantes (`bcmath`/enteros en centavos).

### 8. `mlm-tree-visualizer` (Visualizador Gráfico de Árbol Multinivel)
- **Rol:** Diseña la experiencia visual del árbol binario y escalonado (genealogía). Implementa rendering de alta performance (SVG interactivo / Canvas / HTML ultra-optimizado), zoom/pinch en móviles, dragging/panning, lazy loading de ramas infinitas y tarjetas informativas por nodo.

### 9. `high-concurrency-architect` (Escalabilidad Masiva y Seguridad)
- **Rol:** Prepara la plataforma para millones de transacciones simultáneas de ventas y puntos. Arquitectura basada en Redis Streams/Queues, Workers particionados, Locks atómicos (`Cache::lock`), transacciones ACID con aislamiento estricto y protección contra race conditions.

### 10. `agent-creator` (Creador y Gestor de Agentes)
- **Rol:** Diseña, genera y audita nuevos agentes o subagentes personalizados para el proyecto con las directrices oficiales de Antigravity.

