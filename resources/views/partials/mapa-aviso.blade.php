@unless ($googleMapsKey)
    <div class="border-t border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
        <p class="font-bold">El mapa está desactivado: falta la clave de Google Maps.</p>
        <ol class="mt-2 list-decimal space-y-1 pl-5">
            <li>En Google Cloud habilita <strong>Maps JavaScript API</strong> y crea una clave de API.</li>
            <li>Agrega en el archivo <code>.env</code>: <code>GOOGLE_MAPS_API_KEY=TU_CLAVE</code>.</li>
            <li>Restringe la clave por referenciador a <code>localhost:8000</code> y <code>127.0.0.1:8000</code>.</li>
            <li>Ejecuta <code>php artisan config:clear</code> y recarga la página.</li>
        </ol>
        <p class="mt-2">El ETA y los avisos por voz funcionan igual sin mapa.</p>
    </div>
@endunless
