function contenidoParadero(nombre) {
    const contenido = document.createElement('div');
    contenido.textContent = nombre;

    return contenido;
}

function centroDe(paraderos) {
    if (paraderos.length === 0) {
        return { lat: -12.0583, lng: -77.0833 };
    }

    return {
        lat: paraderos.reduce((suma, paradero) => suma + Number(paradero.latitud), 0) / paraderos.length,
        lng: paraderos.reduce((suma, paradero) => suma + Number(paradero.longitud), 0) / paraderos.length,
    };
}

function crearMapaBase(paraderos, { alSeleccionarParadero } = {}) {
    const L = window.L;
    const elemento = document.getElementById('mapa');

    if (!elemento || !L) {
        return null;
    }

    const centro = centroDe(paraderos);
    const mapa = L.map(elemento).setView([centro.lat, centro.lng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    }).addTo(mapa);

    paraderos.forEach((paradero) => {
        const latitud = Number(paradero.latitud);
        const longitud = Number(paradero.longitud);
        const marcador = L.circleMarker([latitud, longitud], {
            radius: 9,
            color: '#ffffff',
            weight: 2,
            fillColor: '#8c1d40',
            fillOpacity: 1,
        }).addTo(mapa);

        marcador.bindPopup(contenidoParadero(paradero.nombre));
        marcador.on('click', () => alSeleccionarParadero?.(paradero));
    });

    return mapa;
}

export function crearMapaAdmin(paraderos, { alElegir } = {}) {
    const L = window.L;
    const mapa = crearMapaBase(paraderos);

    if (!mapa) {
        return null;
    }

    let marcadorNuevo = null;

    function configurarMarcadorNuevo() {
        marcadorNuevo.on('dragend', () => {
            const posicion = marcadorNuevo.getLatLng();
            alElegir?.(posicion.lat, posicion.lng);
        });
    }

    function elegir(latitud, longitud) {
        const posicion = [Number(latitud), Number(longitud)];

        if (!marcadorNuevo) {
            marcadorNuevo = L.marker(posicion, {
                title: 'Ubicación del nuevo paradero',
                draggable: true,
            }).addTo(mapa);
            configurarMarcadorNuevo();
        } else {
            marcadorNuevo.setLatLng(posicion).addTo(mapa);
        }

        alElegir?.(latitud, longitud);
    }

    mapa.on('click', (evento) => {
        elegir(evento.latlng.lat, evento.latlng.lng);
    });

    return {
        ubicar(latitud, longitud) {
            elegir(latitud, longitud);
            mapa.panTo([Number(latitud), Number(longitud)]);
        },
    };
}

export function crearMapaCampus(paraderos, { alSeleccionarParadero } = {}) {
    const L = window.L;
    const mapa = crearMapaBase(paraderos, { alSeleccionarParadero });

    if (!mapa) {
        return null;
    }

    const centro = centroDe(paraderos);
    const iconoBus = L.divIcon({
        className: 'bus-marker',
        html: '<div style="display:grid;place-items:center;width:100%;height:100%;color:#0f766e;font-size:20px;line-height:1;text-shadow:0 0 2px #fff;">▲</div>',
        iconSize: [28, 28],
        iconAnchor: [14, 14],
    });
    const marcadorBus = L.marker([centro.lat, centro.lng], {
        icon: iconoBus,
        title: 'Bus del campus',
    });

    return {
        actualizarBus(latitud, longitud) {
            marcadorBus.setLatLng([Number(latitud), Number(longitud)]).addTo(mapa);
        },
        centrar(latitud, longitud) {
            mapa.panTo([Number(latitud), Number(longitud)]);
        },
        ocultarBus() {
            marcadorBus.remove();
        },
    };
}
