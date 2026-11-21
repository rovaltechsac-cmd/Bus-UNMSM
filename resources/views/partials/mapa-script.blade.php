<script>
    window.__mapaPendiente = false;
    window.initMapaSanMarcos = function () {
        if (typeof window.__mapaIniciar === 'function') {
            window.__mapaIniciar();
        } else {
            window.__mapaPendiente = true;
        }
    };
</script>
@if ($googleMapsKey)
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsKey }}&callback=initMapaSanMarcos&loading=async" defer></script>
@endif
