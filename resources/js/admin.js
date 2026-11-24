import { crearMapaAdmin } from './mapa.js';

const configuracion = window.configuracionAdmin ?? {};
const latitudInput = document.getElementById('latitud');
const longitudInput = document.getElementById('longitud');
const botonUbicacion = document.getElementById('btn-ubicacion-admin');
const estadoUbicacion = document.getElementById('estado-ubicacion');

let controlMapa = null;

function mostrarUbicacion(latitud, longitud) {
    if (latitudInput) {
        latitudInput.value = Number(latitud).toFixed(7);
    }

    if (longitudInput) {
        longitudInput.value = Number(longitud).toFixed(7);
    }

    if (estadoUbicacion) {
        estadoUbicacion.textContent = 'Ubicación lista. Mueve el marcador si necesitas ajustarla.';
    }
}

function sincronizarInputs() {
    const latitud = Number(latitudInput?.value);
    const longitud = Number(longitudInput?.value);

    if (Number.isFinite(latitud) && Number.isFinite(longitud) && latitudInput?.value !== '' && longitudInput?.value !== '') {
        controlMapa?.ubicar(latitud, longitud);
    }
}

function inicializarMapa() {
    controlMapa = crearMapaAdmin(configuracion.paraderos ?? [], {
        alElegir: mostrarUbicacion,
        tituloMarcador: configuracion.marcadorTitulo,
    });

    sincronizarInputs();
    cargarBus();
}

async function cargarBus() {
    if (!controlMapa || !configuracion.endpoints?.bus) {
        return;
    }

    try {
        const respuesta = await fetch(configuracion.endpoints.bus, {
            headers: { Accept: 'application/json' },
        });

        if (!respuesta.ok) {
            return;
        }

        const datos = await respuesta.json();

        if (datos.activo && datos.bus) {
            controlMapa.actualizarBus(datos.bus.latitud, datos.bus.longitud, {
                rumbo: datos.bus.rumbo,
                rastro: datos.bus.rastro ?? [],
            });
        } else {
            controlMapa.ocultarBus();
        }
    } catch (error) {
        console.warn('No se pudo cargar el bus en el mapa', error);
    }
}

window.__mapaIniciar = inicializarMapa;

if (window.__mapaPendiente) {
    window.__mapaPendiente = false;
    inicializarMapa();
}

botonUbicacion?.addEventListener('click', () => {
    if (!navigator.geolocation) {
        if (estadoUbicacion) {
            estadoUbicacion.textContent = 'Este navegador no puede obtener tu ubicación.';
        }

        return;
    }

    if (estadoUbicacion) {
        estadoUbicacion.textContent = 'Obteniendo ubicación…';
    }

    navigator.geolocation.getCurrentPosition(
        (posicion) => {
            const { latitude, longitude } = posicion.coords;
            mostrarUbicacion(latitude, longitude);
            controlMapa?.ubicar(latitude, longitude);
        },
        (error) => {
            if (estadoUbicacion) {
                estadoUbicacion.textContent = 'No se pudo obtener la ubicación. Puedes tocar el mapa para elegir el punto.';
            }

            console.warn('No se pudo obtener la ubicación del administrador', error.message);
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 5000 },
    );
});

latitudInput?.addEventListener('change', sincronizarInputs);
longitudInput?.addEventListener('change', sincronizarInputs);
setInterval(cargarBus, 5000);
