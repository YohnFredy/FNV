<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Fornuvi') : config('app.name', 'Fornuvi') }}
</title>

@fluxAppearance

<script>
    (function() {
        const appearance = localStorage.getItem('flux.appearance');
        if (appearance === 'dark' || (!appearance && window.matchMedia('(prefers-color-scheme: dark)').matches) || !appearance) {
            document.documentElement.classList.add('dark');
        } else if (appearance === 'light') {
            document.documentElement.classList.remove('dark');
        }
    })();
</script>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@fonts

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Iconos -->
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"
    integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous" />

@vite(['resources/css/app.css', 'resources/js/app.js'])