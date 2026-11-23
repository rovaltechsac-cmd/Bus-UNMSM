@props([
    'etiqueta' => '',
    'valor' => '',
    'detalle' => '',
    'icono' => 'circulo',
    'destacado' => false,
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-line bg-surface p-5 shadow-sm']) }}>
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="truncate text-xs font-bold uppercase tracking-[0.12em] text-muted">{{ $etiqueta }}</p>
            <p class="mt-2 text-3xl font-extrabold tracking-tight {{ $destacado ? 'text-brand' : 'text-ink' }}">{{ $valor }}</p>
            <p class="mt-2 truncate text-xs text-muted">{{ $detalle }}</p>
        </div>
        <span class="grid size-10 shrink-0 place-items-center rounded-xl border {{ $destacado ? 'border-brand/30 bg-brand/10 text-brand' : 'border-line bg-surface-alt text-metallic' }}">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                @switch($icono)
                    @case('bus')
                        <rect x="3" y="5" width="18" height="12" rx="2"></rect>
                        <path d="M3 11h18M7 17v2M17 17v2"></path>
                        @break
                    @case('paraderos')
                        <path d="M12 21s-7-5.1-7-11a7 7 0 1 1 14 0c0 5.9-7 11-7 11Z"></path>
                        <circle cx="12" cy="10" r="2.5"></circle>
                        @break
                    @case('rutas')
                        <path d="M4 19V7a3 3 0 0 1 3-3h6a3 3 0 0 1 3 3v10"></path>
                        <path d="M16 10h1a3 3 0 0 1 3 3v6M8 15h5"></path>
                        <circle cx="8" cy="19" r="1.5"></circle>
                        <circle cx="16" cy="19" r="1.5"></circle>
                        @break
                    @case('usuarios')
                        <circle cx="9" cy="8" r="3"></circle>
                        <path d="M3 20a6 6 0 0 1 12 0M16 5a3 3 0 0 1 0 6M18 20a6 6 0 0 0-3-5.2"></path>
                        @break
                    @case('alertas')
                        <path d="M12 3 2.5 20h19L12 3Z"></path>
                        <path d="M12 9v5m0 3h.01"></path>
                        @break
                    @case('reloj')
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 7v5l3.5 2"></path>
                        @break
                    @case('check')
                        <path d="m5 12.5 4.5 4.5L19 7"></path>
                        @break
                    @default
                        <circle cx="12" cy="12" r="9"></circle>
                @endswitch
            </svg>
        </span>
    </div>
</div>
