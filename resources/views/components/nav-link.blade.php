@props([
    'ruta' => '#',
    'etiqueta' => '',
    'icono' => 'circulo',
    'activo' => false,
])

<a href="{{ $ruta }}" {{ $attributes->merge([
    'class' => 'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition focus:outline-none focus:ring-4 focus:ring-brand/20 '
        .($activo
            ? 'bg-brand/10 text-ink ring-1 ring-brand/30'
            : 'text-muted hover:bg-surface-alt hover:text-ink'),
]) }}>
    <svg class="size-5 shrink-0 {{ $activo ? 'text-brand' : 'text-metallic' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        @switch($icono)
            @case('resumen')
                <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                @break
            @case('paraderos')
                <path d="M12 21s-7-5.1-7-11a7 7 0 1 1 14 0c0 5.9-7 11-7 11Z"></path>
                <circle cx="12" cy="10" r="2.5"></circle>
                @break
            @case('bus')
                <rect x="3" y="5" width="18" height="12" rx="2"></rect>
                <path d="M3 11h18M7 17v2M17 17v2"></path>
                <circle cx="7.5" cy="14" r="0.6"></circle>
                <circle cx="16.5" cy="14" r="0.6"></circle>
                @break
            @case('accesible')
                <circle cx="12" cy="4.5" r="1.8"></circle>
                <path d="M4 8.5h16M12 8.5v6m0 0-4 6m4-6 4 6"></path>
                @break
            @case('clave')
                <circle cx="8" cy="15" r="4"></circle>
                <path d="m11 12 8-8 2 2-2 2 1.5 1.5-2.5 2.5-1.5-1.5-4 4"></path>
                @break
            @default
                <circle cx="12" cy="12" r="9"></circle>
        @endswitch
    </svg>
    <span class="truncate">{{ $etiqueta }}</span>
</a>
