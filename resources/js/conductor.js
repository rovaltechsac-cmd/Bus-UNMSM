import { hablar, notificar, activarNotificaciones } from './audio.js';
import { crearMapaCampus } from './mapa.js';

const configuracion = window.configuracionConductor;
const token = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const listaAlertas = document.getElementById('lista-alertas');
const sinAlertas = document.getElementById('sin-alertas');
const contadorAlertas = document.getElementById('contador-alertas');
const indicadorGps = document.getElementById('indicador-gps');
const estadoGps = document.getElementById('estado-gps');
const detalleGps = document.getElementById('detalle-gps');
const estadoNotificaciones = document.getElementById('estado-notificaciones');
const botonNotificaciones = document.getElementById('btn-notificaciones');
const botonRecargar = document.getElementById('btn-recargar');
const botonReintentarGps = document.getElementById('btn-reintentar-gps');
const casillaVoz = document.getElementById('conductor-voz');

let vozActiva = localStorage.getItem('burrito.conductor.voz') !== '0';
casillaVoz.checked = vozActiva;

const avisosConocidos = new Set();
const paraderos = configuracion.paraderos ?? [];
let controlMapa = null;
let watchId = null;
let ultimoEnvio = 0;
let cargandoAlertas = false;

function endpointConId(plantilla, id) {
    return plantilla.replace('__ID__', encodeURIComponent(id));
}

function inicializarMapa() {
    controlMapa = crearMapaCampus(paraderos);
    cargarBus();
}

async function cargarBus() {
    if (!controlMapa) {
        return;
    }

    try {
        const datos = await peticion(configuracion.endpoints.bus);

        if (datos.activo && datos.bus) {
            controlMapa.actualizarBus(datos.bus.latitud, datos.bus.longitud, {
                rumbo: datos.bus.rumbo,
                rastro: datos.bus.rastro ?? [],
            });
        }
    } catch (error) {
        console.warn('No se pudo cargar el estado del bus', error);
    }
}

window.__mapaIniciar = inicializarMapa;

if (window.__mapaPendiente) {
    window.__mapaPendiente = false;
    inicializarMapa();
}

async function peticion(url, opciones = {}) {
    const respuesta = await fetch(url, {
        ...opciones,
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': token,
            ...(opciones.body ? { 'Content-Type': 'application/json' } : {}),
            ...(opciones.headers ?? {}),
        },
    });

    if (!respuesta.ok) {
        let mensaje = `Error ${respuesta.status}`;

        try {
            const cuerpo = await respuesta.json();
            mensaje = cuerpo.message ?? mensaje;
        } catch (error) {
            console.warn('Respuesta no JSON del servidor', error);
        }

        throw new Error(mensaje);
    }

    return respuesta.json();
}

function iniciarGeolocalizacion() {
    if (!navigator.geolocation) {
        indicadorGps.className = 'size-3 shrink-0 rounded-full bg-brand-hover';
        estadoGps.textContent = 'Este navegador no soporta geolocalización';
        return;
    }

    estadoGps.textContent = 'Esperando señal GPS…';

    if (watchId !== null) {
        navigator.geolocation.clearWatch(watchId);
    }

    watchId = navigator.geolocation.watchPosition(
        async (posicion) => {
            const { latitude, longitude, heading, accuracy } = posicion.coords;
            const ahora = Date.now();

            indicadorGps.className = 'size-3 shrink-0 rounded-full bg-brand';
            estadoGps.textContent = 'Ubicación obtenida';
            detalleGps.textContent = `Lat ${latitude.toFixed(6)} · Lon ${longitude.toFixed(6)} · ±${Math.round(accuracy)} m`;

            if (controlMapa) {
                controlMapa.actualizarBus(latitude, longitude, {
                    rumbo: heading === null ? null : heading,
                });
                controlMapa.centrar(latitude, longitude);
            }

            if (ahora - ultimoEnvio < 4000) {
                return;
            }
            ultimoEnvio = ahora;

            try {
                const datos = await peticion(configuracion.endpoints.ubicacion, {
                    method: 'POST',
                    body: JSON.stringify({
                        latitud: latitude,
                        longitud: longitude,
                        rumbo: heading === null ? null : Number(heading.toFixed(2)),
                        exactitud_m: Math.round(accuracy),
                    }),
                });

                controlMapa?.actualizarBus(latitude, longitude, {
                    rumbo: heading === null ? null : heading,
                    rastro: datos.rastro ?? [],
                });
                detalleGps.textContent += ' · Enviada al servidor';
            } catch (error) {
                estadoGps.textContent = 'No se pudo enviar la ubicación';
                detalleGps.textContent = error.message;
            }
        },
        (error) => {
            indicadorGps.className = 'size-3 shrink-0 rounded-full bg-brand-hover';
            estadoGps.textContent = 'No se pudo acceder al GPS';
            detalleGps.textContent = error.message;
        },
        { enableHighAccuracy: true, maximumAge: 3000, timeout: 12000 },
    );
}

function crearTarjeta(alerta) {
    const articulo = document.createElement('article');
    articulo.className = `rounded-xl border p-4 ${alerta.estado === 'pendiente' ? 'border-brand/40 bg-brand/10' : 'border-line bg-surface-alt'}`;

    const titulo = document.createElement('h3');
    titulo.className = 'text-base font-bold text-ink';
    titulo.textContent = `Persona invidente en: ${alerta.paradero.nombre}`;
    articulo.append(titulo);

    const detalle = document.createElement('p');
    detalle.className = 'mt-1 text-xs text-muted';
    detalle.textContent = `${alerta.pasajero?.name ?? 'Pasajero'} · Estado: ${alerta.estado.replaceAll('_', ' ')}`;
    articulo.append(detalle);

    if (alerta.nota) {
        const nota = document.createElement('p');
        nota.className = 'mt-1 text-sm text-ink';
        nota.textContent = alerta.nota;
        articulo.append(nota);
    }

    const boton = document.createElement('button');
    boton.type = 'button';
    boton.dataset.id = String(alerta.id);
    boton.dataset.accion = alerta.estado === 'pendiente' ? 'atender' : 'finalizar';
    boton.className = 'mt-3 w-full rounded-lg bg-brand px-3 py-2.5 text-sm font-bold text-white transition hover:bg-brand-hover focus:outline-none focus:ring-4 focus:ring-brand/30';
    boton.textContent = alerta.estado === 'pendiente' ? 'Ya estoy atendiendo' : 'Marcar como atendido';
    articulo.append(boton);

    return articulo;
}

function anunciarAlerta(alerta) {
    const texto = `Atención. Persona invidente esperando en el paradero ${alerta.paradero.nombre}.`;
    hablar(texto, vozActiva);
    notificar('Pasajero invidente en el paradero', `Paradero ${alerta.paradero.nombre}`);
}

async function cargarAlertas() {
    if (cargandoAlertas) {
        return;
    }
    cargandoAlertas = true;

    try {
        const datos = await peticion(configuracion.endpoints.alertas);
        const alertas = datos.alertas ?? [];
        const idsActuales = new Set(alertas.map((alerta) => alerta.id));

        for (const id of avisosConocidos) {
            if (!idsActuales.has(id)) {
                avisosConocidos.delete(id);
            }
        }

        sinAlertas.hidden = alertas.length > 0;
        contadorAlertas.textContent = String(alertas.length);
        listaAlertas.replaceChildren(...alertas.map(crearTarjeta));

        for (const alerta of alertas) {
            if (!avisosConocidos.has(alerta.id)) {
                avisosConocidos.add(alerta.id);
                anunciarAlerta(alerta);
            }
        }
    } catch (error) {
        sinAlertas.textContent = `No se pudieron cargar los avisos: ${error.message}`;
    } finally {
        cargandoAlertas = false;
    }
}

listaAlertas.addEventListener('click', async (evento) => {
    const boton = evento.target.closest('button[data-accion]');

    if (!boton) {
        return;
    }

    boton.disabled = true;

    try {
        const plantilla = boton.dataset.accion === 'atender'
            ? configuracion.endpoints.atender
            : configuracion.endpoints.finalizar;

        await peticion(endpointConId(plantilla, boton.dataset.id), { method: 'POST' });
        await cargarAlertas();
    } catch (error) {
        boton.disabled = false;
        window.alert(error.message);
    }
});

casillaVoz.addEventListener('change', () => {
    vozActiva = casillaVoz.checked;
    localStorage.setItem('burrito.conductor.voz', vozActiva ? '1' : '0');
    hablar('Altavoz activado', vozActiva);
});

botonNotificaciones.addEventListener('click', async () => {
    const resultado = await activarNotificaciones();
    const mensajes = {
        concedido: 'Notificaciones activadas.',
        denegado: 'El navegador bloqueó las notificaciones.',
        'no-soportado': 'Este navegador no soporta notificaciones.',
        default: 'No se concedió permiso de notificaciones.',
    };
    estadoNotificaciones.textContent = mensajes[resultado] ?? mensajes.default;
});

botonRecargar?.addEventListener('click', cargarAlertas);
botonReintentarGps?.addEventListener('click', iniciarGeolocalizacion);

if ('wakeLock' in navigator) {
    navigator.wakeLock.request('screen').catch(() => {});
}

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(() => {});
}

iniciarGeolocalizacion();
cargarAlertas();
setInterval(cargarAlertas, 4000);
setInterval(cargarBus, 5000);
