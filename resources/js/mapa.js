function contenidoParadero(nombre) {
    const contenido = document.createElement('div');
    contenido.textContent = nombre;

    return contenido;
}

export function crearMapaAdmin(paraderos, { alElegir } = {}) {
    const elemento = document.getElementById('mapa');

    if (!elemento || !window.google?.maps) {
        return null;
    }

    const centro = paraderos.length > 0
        ? {
            lat: paraderos.reduce((suma, paradero) => suma + Number(paradero.latitud), 0) / paraderos.length,
            lng: paraderos.reduce((suma, paradero) => suma + Number(paradero.longitud), 0) / paraderos.length,
        }
        : { lat: -12.0583, lng: -77.0833 };

    const mapa = new google.maps.Map(elemento, {
        center: centro,
        zoom: 16,
        mapTypeControl: false,
        streetViewControl: false,
    });

    paraderos.forEach((paradero) => {
        const marcador = new google.maps.Marker({
            position: { lat: Number(paradero.latitud), lng: Number(paradero.longitud) },
            map: mapa,
            title: paradero.nombre,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 11,
                fillColor: '#8c1d40',
                fillOpacity: 1,
                strokeColor: '#ffffff',
                strokeWeight: 2,
            },
        });

        const informacion = new google.maps.InfoWindow({
            content: contenidoParadero(`${paradero.nombre} · ${paradero.latitud}, ${paradero.longitud}`),
        });

        marcador.addListener('click', () => informacion.open(mapa, marcador));
    });

    const marcadorNuevo = new google.maps.Marker({
        map: mapa,
        title: 'Ubicación del nuevo paradero',
        draggable: true,
        visible: false,
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 13,
            fillColor: '#0f766e',
            fillOpacity: 1,
            strokeColor: '#ffffff',
            strokeWeight: 3,
        },
    });

    function elegir(latitud, longitud) {
        marcadorNuevo.setPosition({ lat: latitud, lng: longitud });
        marcadorNuevo.setVisible(true);
        alElegir?.(latitud, longitud);
    }

    mapa.addListener('click', (evento) => {
        elegir(evento.latLng.lat(), evento.latLng.lng());
    });

    marcadorNuevo.addListener('dragend', () => {
        const posicion = marcadorNuevo.getPosition();

        if (posicion) {
            alElegir?.(posicion.lat(), posicion.lng());
        }
    });

    return {
        ubicar(latitud, longitud) {
            elegir(latitud, longitud);
            mapa.panTo({ lat: latitud, lng: longitud });
        },
    };
}

export function crearMapaCampus(paraderos, { alSeleccionarParadero } = {}) {
    const elemento = document.getElementById('mapa');

    if (!elemento || !window.google?.maps) {
        return null;
    }

    const centro = paraderos.length > 0
        ? {
            lat: paraderos.reduce((suma, paradero) => suma + paradero.latitud, 0) / paraderos.length,
            lng: paraderos.reduce((suma, paradero) => suma + paradero.longitud, 0) / paraderos.length,
        }
        : { lat: -12.0583, lng: -77.0833 };

    const mapa = new google.maps.Map(elemento, {
        center: centro,
        zoom: 16,
        mapTypeControl: false,
        streetViewControl: false,
    });

    paraderos.forEach((paradero) => {
        const marcador = new google.maps.Marker({
            position: { lat: paradero.latitud, lng: paradero.longitud },
            map: mapa,
            title: paradero.nombre,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 11,
                fillColor: '#8c1d40',
                fillOpacity: 1,
                strokeColor: '#ffffff',
                strokeWeight: 2,
            },
        });

        const informacion = new google.maps.InfoWindow({
            content: contenidoParadero(paradero.nombre),
        });

        marcador.addListener('click', () => {
            informacion.open(mapa, marcador);
            alSeleccionarParadero?.(paradero);
        });
    });

    const marcadorBus = new google.maps.Marker({
        map: mapa,
        title: 'Bus del campus',
        visible: false,
        icon: {
            path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
            scale: 7,
            fillColor: '#0f766e',
            fillOpacity: 1,
            strokeColor: '#ffffff',
            strokeWeight: 2,
        },
    });

    return {
        actualizarBus(latitud, longitud) {
            marcadorBus.setPosition({ lat: latitud, lng: longitud });
            marcadorBus.setVisible(true);
        },
        centrar(latitud, longitud) {
            mapa.panTo({ lat: latitud, lng: longitud });
        },
        ocultarBus() {
            marcadorBus.setVisible(false);
        },
    };
}
