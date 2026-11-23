function contenidoParadero(nombre) {
    const contenido = document.createElement('div');
    contenido.textContent = nombre;

    return contenido;
}

const CENTRO_UNMSM = { lat: -12.0583, lng: -77.0833 };

function iconoParadero(activo) {
    const L = window.L;
    const color = activo ? '#22C55E' : '#EF4444';

    return L.divIcon({
        className: 'pin-paradero',
        html: `<svg viewBox="0 0 32 42" width="32" height="42" aria-hidden="true">
            <path d="M16 2C8.3 2 2 8.3 2 16c0 10.5 14 24 14 24s14-13.5 14-24C30 8.3 23.7 2 16 2Z" fill="${color}" stroke="#0B0C0E" stroke-width="2" stroke-linejoin="round"></path>
            <circle cx="16" cy="16" r="5.5" fill="#FFFFFF"></circle>
        </svg>`,
        iconSize: [32, 42],
        iconAnchor: [16, 42],
        popupAnchor: [0, -40],
    });
}

function crearMapaBase(paraderos, { alSeleccionarParadero } = {}) {
    const L = window.L;
    const elemento = document.getElementById('mapa');

    if (!elemento || !L) {
        return null;
    }

    const mapa = L.map(elemento).setView([CENTRO_UNMSM.lat, CENTRO_UNMSM.lng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    }).addTo(mapa);

    paraderos.forEach((paradero) => {
        const latitud = Number(paradero.latitud);
        const longitud = Number(paradero.longitud);
        const marcador = L.marker([latitud, longitud], {
            icon: iconoParadero(paradero.activo !== false),
            title: paradero.nombre,
        }).addTo(mapa);

        marcador.bindPopup(contenidoParadero(paradero.nombre));
        marcador.on('click', () => alSeleccionarParadero?.(paradero));
    });

    return mapa;
}

export function crearMapaAdmin(paraderos, { alElegir, tituloMarcador } = {}) {
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
                title: tituloMarcador ?? 'Ubicación del nuevo paradero',
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

    const iconoBus = L.divIcon({
        className: 'bus-marker',
        html: '<div style="display:grid;place-items:center;width:100%;height:100%;color:#B91C1C;font-size:20px;line-height:1;text-shadow:0 0 2px #0B0C0E;">▲</div>',
        iconSize: [28, 28],
        iconAnchor: [14, 14],
    });
    const marcadorBus = L.marker([CENTRO_UNMSM.lat, CENTRO_UNMSM.lng], {
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
