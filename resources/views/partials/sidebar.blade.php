<aside id="sidebar-principal"
       class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-line bg-surface lg:translate-x-0"
       aria-label="Navegación principal">
    <div class="flex h-16 items-center gap-3 border-b border-line px-5">
        <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-brand text-white shadow-lg shadow-brand/20">
            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M4 7.5h16M4 12h16M4 16.5h16"></path>
                <circle cx="7" cy="7.5" r="1.4" fill="currentColor" stroke="none"></circle>
                <circle cx="17" cy="16.5" r="1.4" fill="currentColor" stroke="none"></circle>
            </svg>
        </span>
        <div class="min-w-0">
            <p class="truncate text-base font-extrabold tracking-tight text-ink">BusUNMS</p>
            <p class="truncate text-[11px] font-semibold uppercase tracking-[0.14em] text-metallic">ROVAL TECH · UNMSM</p>
        </div>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-5">
        <p class="px-3 pb-2 text-[11px] font-bold uppercase tracking-[0.16em] text-metallic">Navegación</p>

        <x-nav-link ruta="{{ route('dashboard.index') }}" etiqueta="Resumen" icono="resumen" :activo="($pagina ?? '') === 'dashboard'" />

        @if (auth()->user()?->esAdministrador())
            <x-nav-link ruta="{{ route('admin.index') }}" etiqueta="Administración" icono="paraderos" :activo="($pagina ?? '') === 'admin'" />
            <x-nav-link ruta="{{ route('admin.index') }}" etiqueta="Paraderos" icono="paraderos" />
            <x-nav-link ruta="{{ route('pasajero.index') }}" etiqueta="Vista del pasajero" icono="bus" />
        @endif

        @if (auth()->user()?->esConductor())
            <x-nav-link ruta="{{ route('conductor.index') }}" etiqueta="Mi viaje" icono="bus" :activo="($pagina ?? '') === 'conductor'" />
            <x-nav-link ruta="{{ route('pasajero.index') }}" etiqueta="Paraderos" icono="paraderos" />
        @endif

        @if (auth()->user()?->esInvidente())
            <x-nav-link ruta="{{ route('pasajero.invidente.index') }}" etiqueta="Panel accesible" icono="accesible" :activo="($pagina ?? '') === 'invidente'" />
        @else
            <x-nav-link ruta="{{ route('pasajero.index') }}" etiqueta="Mi bus" icono="bus" :activo="($pagina ?? '') === 'pasajero'" />
            <x-nav-link ruta="{{ route('pasajero.index') }}" etiqueta="Mapa" icono="paraderos" />
        @endif

        @if (auth()->user()?->puedeCambiarClave())
            <div class="pt-5">
                <p class="px-3 pb-2 text-[11px] font-bold uppercase tracking-[0.16em] text-metallic">Cuenta</p>
                <x-nav-link ruta="{{ route('clave.edit') }}" etiqueta="Cambiar clave" icono="clave" :activo="request()->routeIs('clave.edit')" />
            </div>
        @endif
    </nav>

    <div class="border-t border-line p-4">
        <div class="mb-3 flex items-center gap-3 rounded-xl border border-line bg-surface-alt p-3">
            <span class="grid size-9 shrink-0 place-items-center rounded-full border border-line bg-brand text-xs font-extrabold text-white">
                {{ strtoupper(mb_substr(auth()->user()?->name ?? '?', 0, 1)) }}
            </span>
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-ink">{{ auth()->user()?->name }}</p>
                <p class="truncate text-xs text-muted">{{ auth()->user()?->codigo ?? auth()->user()?->email }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-line px-3 py-2.5 text-sm font-bold text-muted transition hover:border-brand hover:text-ink focus:outline-none focus:ring-4 focus:ring-brand/20">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"></path>
                </svg>
                Cerrar sesión
            </button>
        </form>
    </div>
</aside>
