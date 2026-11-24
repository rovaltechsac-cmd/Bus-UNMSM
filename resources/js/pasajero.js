import { hablar } from './audio.js';
import { crearMapaCampus } from './mapa.js';

const configuracion = window.configuracionPasajero;
const token = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const selectorParadero = document.getElementById('paradero');
const estadoBus = document.getElementById('estado-bus');
const detalleBus = document.getElementById('detalle-bus');
const botonUbicacion = document.getElementById('btn-ubicacion');
const casillaAudio = document.getElementById('audio-activo');
const estadoAviso = document.getElementById('estado-aviso');
const paraderoNombre = document.getElementById('paradero-nombre');

const paraderos = configuracion.paraderos ?? [];
const modoInvidente = Boolean(configuracion.esInvidenteInicial);
const radioParadero = Number(configuracion.radioParadero ?? 40);
const preferenciaVoz = localStorage.getItem('burrito.pasajero.audio');
let controlMapa = null;
let ultimoTextoVoz = '';
let cargandoBus = false;
let ultimoAvisoParaderoId = null;
let vozActiva = preferenciaVoz === null
    ? Boolean(configuracion.vozActivaInicial)
    : preferenciaVoz === '1';

if (casillaAudio) {
    casillaAudio.checked = vozActiva;
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

function distanciaMetros(origen, destino) {
    const radio = 6371000;
    const lat1 = (origen.latitud * Math.PI) / 180;
    const lat2 = (destino.latitud * Math.PI) / 180;
    const dLat = lat2 - lat1;
    const dLon = ((destino.longitud - origen.longitud) * Math.PI) / 180;

    const a = Math.sin(dLat / 2) ** 2
        + Math.cos(lat1) * Math.cos(lat2) * Math.sin(dLon / 2) ** 2;

    return radio * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

function formatearHora(iso) {
    if (!iso) {
        return '—';
    }

    return new Intl.DateTimeFormat('es-PE', { hour: '2-digit', minute: '2-digit' }).format(new Date(iso));
}

function nombreParadero(id) {
    return paraderos.find((paradero) => String(paradero.id) === String(id))?.nombre ?? 'tu paradero';
}

function textoParaVoz(datos, paradero) {
    if (!datos.activo || !datos.bus) {
        return modoInvidente ? 'El bus todavía no tiene señal.' : '';
    }

    if (datos.eta?.llega_pronto) {
        return `El bus ya llegó a ${paradero}.`;
    }

    const minutos = datos.eta?.minutos;

    if (minutos === null || minutos === undefined) {
        return '';
    }

    if (modoInvidente) {
        return minutos <= 1
            ? `Bus a un minuto de ${paradero}.`
            : `Faltan ${minutos} minutos para que llegue el bus a ${paradero}.`;
    }

    return minutos <= 1 ? 'Bus a un minuto.' : '';
}

function inicializarMapa() {
    controlMapa = crearMapaCampus(paraderos, {
        alSeleccionarParadero: (paradero) => {
            if (!selectorParadero) {
                return;
            }

            selectorParadero.value = String(paradero.id);

            if (paraderoNombre) {
                paraderoNombre.textContent = paradero.nombre;
            }

            ultimoTextoVoz = '';
            actualizarEstado();
        },
    });
}

window.__mapaIniciar = inicializarMapa;

if (window.__mapaPendiente) {
    window.__mapaPendiente = false;
    inicializarMapa();
}

async function actualizarEstado() {
    const paraderoId = selectorParadero?.value;

    if (!paraderoId || cargandoBus) {
        return;
    }
    cargandoBus = true;

    try {
        const datos = await peticion(`${configuracion.endpoints.bus}?paradero_id=${encodeURIComponent(paraderoId)}`);
        const paradero = nombreParadero(paraderoId);
        let texto;

        if (!datos.activo || !datos.bus) {
            texto = 'Bus sin señal en este momento';
        } else if (!datos.eta) {
            texto = 'Ubicación disponible';
        } else {
            texto = datos.eta.texto;
        }

        estadoBus.textContent = texto;
        detalleBus.textContent = datos.bus
            ? `Distancia: ${Math.round(datos.eta?.distancia_m ?? 0)} m · Actualizado: ${formatearHora(datos.bus.actualizado_at)}`
            : 'El conductor aún no ha compartido su ubicación.';

        const textoVoz = textoParaVoz(datos, paradero);

        if (vozActiva && textoVoz && textoVoz !== ultimoTextoVoz) {
            ultimoTextoVoz = textoVoz;
            hablar(textoVoz, true);
        }

        if (controlMapa) {
            if (datos.bus) {
                controlMapa.actualizarBus(datos.bus.latitud, datos.bus.longitud, {
                    rumbo: datos.bus.rumbo,
                    rastro: datos.bus.rastro ?? [],
                });
            } else {
                controlMapa.ocultarBus();
            }
        }
    } catch (error) {
        estadoBus.textContent = 'No se pudo consultar el estado';
        detalleBus.textContent = error.message;
    } finally {
        cargandoBus = false;
    }
}

async function enviarAvisoAutomatico(paraderoId) {
    if (ultimoAvisoParaderoId === paraderoId) {
        if (estadoAviso) {
            estadoAviso.textContent = 'El conductor ya sabe que estás en este paradero.';
        }

        return;
    }

    if (estadoAviso) {
        estadoAviso.textContent = 'Avisando al conductor…';
    }

    try {
        const datos = await peticion(configuracion.endpoints.alertas, {
            method: 'POST',
            body: JSON.stringify({
                paradero_id: Number(paraderoId),
                nota: 'Persona invidente esperando en el paradero',
            }),
        });

        ultimoAvisoParaderoId = paraderoId;

        if (estadoAviso) {
            estadoAviso.textContent = datos.mensaje ?? 'El conductor ya sabe que estás aquí.';
        }

        hablar('Aviso enviado. El conductor ya sabe que estás en el paradero.', vozActiva);
    } catch (error) {
        if (estadoAviso) {
            estadoAviso.textContent = error.message;
        }
    }
}

function detectarParaderoAutomaticamente() {
    if (!modoInvidente) {
        return;
    }

    if (!navigator.geolocation) {
        if (estadoAviso) {
            estadoAviso.textContent = 'Este navegador no puede detectar tu paradero.';
        }

        return;
    }

    navigator.geolocation.getCurrentPosition(
        (posicion) => {
            const origen = {
                latitud: posicion.coords.latitude,
                longitud: posicion.coords.longitude,
            };
            const masCercano = paraderos.reduce((mejor, paradero) => {
                const distancia = distanciaMetros(origen, paradero);

                return mejor === null || distancia < mejor.distancia
                    ? { paradero, distancia }
                    : mejor;
            }, null);

            const tolerancia = radioParadero + Math.min(posicion.coords.accuracy ?? 0, 50);

            if (!masCercano || masCercano.distancia > tolerancia) {
                if (paraderoNombre) {
                    paraderoNombre.textContent = 'No detectado';
                }

                if (estadoAviso) {
                    estadoAviso.textContent = 'Acércate a un paradero para avisar automáticamente.';
                }

                return;
            }

            if (selectorParadero) {
                selectorParadero.value = String(masCercano.paradero.id);
            }

            if (paraderoNombre) {
                paraderoNombre.textContent = masCercano.paradero.nombre;
            }

            ultimoTextoVoz = '';
            actualizarEstado();
            enviarAvisoAutomatico(masCercano.paradero.id);
        },
        (error) => {
            if (estadoAviso) {
                estadoAviso.textContent = 'Activa la ubicación para avisar automáticamente.';
            }

            if (paraderoNombre) {
                paraderoNombre.textContent = 'Ubicación no disponible';
            }

            console.warn('No se pudo detectar el paradero', error.message);
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 5000 },
    );
}

selectorParadero?.addEventListener('change', () => {
    if (paraderoNombre) {
        paraderoNombre.textContent = nombreParadero(selectorParadero.value);
    }

    ultimoTextoVoz = '';
    actualizarEstado();
});

botonUbicacion?.addEventListener('click', () => {
    if (!navigator.geolocation) {
        window.alert('Este navegador no soporta geolocalización.');
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (posicion) => {
            const origen = { latitud: posicion.coords.latitude, longitud: posicion.coords.longitude };
            const masCercano = paraderos.reduce((mejor, paradero) => {
                const distancia = distanciaMetros(origen, paradero);

                return mejor === null || distancia < mejor.distancia
                    ? { paradero, distancia }
                    : mejor;
            }, null);

            if (masCercano && selectorParadero) {
                selectorParadero.value = String(masCercano.paradero.id);
                ultimoTextoVoz = '';
                actualizarEstado();
            }
        },
        (error) => window.alert(error.message),
        { enableHighAccuracy: true, timeout: 10000 },
    );
});

casillaAudio?.addEventListener('change', () => {
    vozActiva = casillaAudio.checked;
    localStorage.setItem('burrito.pasajero.audio', vozActiva ? '1' : '0');

    if (vozActiva) {
        hablar('Avisos de audio activados', true);
    }
});

if (modoInvidente) {
    detectarParaderoAutomaticamente();
    setInterval(detectarParaderoAutomaticamente, 60000);
} else {
    actualizarEstado();
}

setInterval(actualizarEstado, 5000);
