<script>
    window.__mapaPendiente = false;
    window.__leafletListo = function () {
        if (typeof window.__mapaIniciar === 'function') {
            window.__mapaIniciar();
        } else {
            window.__mapaPendiente = true;
        }
    };
</script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer onload="window.__leafletListo()"></script>
