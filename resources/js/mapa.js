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

function crearSeguimientoBus(mapa) {
    const L = window.L;

    const iconoBus = L.divIcon({
        className: 'bus-marker',
        html: `<div class="bus-icono" style="display:grid;place-items:center;width:100%;height:100%;transform-origin:center;">
            <svg viewBox="0 0 32 32" width="32" height="32" aria-hidden="true">
                <circle cx="16" cy="16" r="15" fill="#0B0C0E" opacity="0.85"></circle>
                <rect x="7" y="6" width="18" height="17" rx="4" fill="#3B82F6" stroke="#F3F4F6" stroke-width="1.5"></rect>
                <rect x="10" y="9" width="12" height="6" rx="1.5" fill="#BFDBFE"></rect>
                <circle cx="12" cy="25" r="2.2" fill="#F3F4F6"></circle>
                <circle cx="20" cy="25" r="2.2" fill="#F3F4F6"></circle>
            </svg>
        </div>`,
        iconSize: [36, 36],
        iconAnchor: [18, 18],
        popupAnchor: [0, -18],
    });

    const marcadorBus = L.marker([CENTRO_UNMSM.lat, CENTRO_UNMSM.lng], {
        icon: iconoBus,
        title: 'Bus en vivo',
    });
    const lineaRastro = L.polyline([], {
        color: '#3B82F6',
        weight: 4,
        opacity: 0.85,
        lineCap: 'round',
        lineJoin: 'round',
    });

    function aplicarRumbo(rumbo) {
        const icono = marcadorBus.getElement()?.querySelector('.bus-icono');

        if (icono) {
            icono.style.transform = `rotate(${Number(rumbo) || 0}deg)`;
        }
    }

    return {
        actualizar(latitud, longitud, { rumbo = null, rastro = [] } = {}) {
            marcadorBus.setLatLng([Number(latitud), Number(longitud)]).addTo(mapa);
            requestAnimationFrame(() => aplicarRumbo(rumbo));
            lineaRastro
                .setLatLngs(rastro.map((punto) => [Number(punto.latitud), Number(punto.longitud)]))
                .addTo(mapa);
        },
        ocultar() {
            marcadorBus.remove();
            lineaRastro.remove();
        },
    };
}

export function crearMapaAdmin(paraderos, { alElegir, tituloMarcador } = {}) {
    const L = window.L;
    const mapa = crearMapaBase(paraderos);

    if (!mapa) {
        return null;
    }

    let marcadorNuevo = null;
    const seguimientoBus = crearSeguimientoBus(mapa);

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
        actualizarBus(latitud, longitud, opciones = {}) {
            seguimientoBus.actualizar(latitud, longitud, opciones);
        },
        ocultarBus() {
            seguimientoBus.ocultar();
        },
    };
}

export function crearMapaCampus(paraderos, { alSeleccionarParadero } = {}) {
    const mapa = crearMapaBase(paraderos, { alSeleccionarParadero });

    if (!mapa) {
        return null;
    }

    const seguimientoBus = crearSeguimientoBus(mapa);

    return {
        actualizarBus(latitud, longitud, opciones = {}) {
            seguimientoBus.actualizar(latitud, longitud, opciones);
        },
        centrar(latitud, longitud) {
            mapa.panTo([Number(latitud), Number(longitud)]);
        },
        ocultarBus() {
            seguimientoBus.ocultar();
        },
    };
}
