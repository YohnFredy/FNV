@props(['title' => null, 'maxWidth' => 'max-w-sm'])

<x-layouts::auth.simple :title="$title" :max-width="$maxWidth">
    {{ $slot }}
</x-layouts::auth.simple>
