<script>
    window.configuracionPasajero = {{ Illuminate\Support\Js::from([
        'paraderos' => $paraderos->map(fn ($paradero) => [
            'id' => $paradero->id,
            'nombre' => $paradero->nombre,
            'latitud' => (float) $paradero->latitud,
            'longitud' => (float) $paradero->longitud,
        ]),
        'endpoints' => [
            'bus' => route('api.bus.show'),
            'alertas' => route('api.alertas.store'),
            'mias' => route('api.alertas.mias'),
            'cancelar' => route('api.alertas.cancelar', ['alerta' => '__ID__']),
        ],
        'radioParadero' => (float) $radioParadero,
        'esInvidenteInicial' => (bool) $esInvidenteInicial,
        'vozActivaInicial' => (bool) $esInvidenteInicial,
    ]) }};
</script>
@include('partials.mapa-script')

