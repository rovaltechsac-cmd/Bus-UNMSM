export function hablar(texto, habilitado = true) {
    if (!habilitado || !texto || !('speechSynthesis' in window)) {
        return;
    }

    window.speechSynthesis.cancel();

    const mensaje = new SpeechSynthesisUtterance(texto);
    mensaje.lang = 'es-PE';
    mensaje.rate = 0.95;
    mensaje.volume = 1;
    window.speechSynthesis.speak(mensaje);
}

export async function activarNotificaciones() {
    if (!('Notification' in window)) {
        return 'no-soportado';
    }

    if (Notification.permission === 'granted') {
        return 'concedido';
    }

    if (Notification.permission === 'denied') {
        return 'denegado';
    }

    return await Notification.requestPermission();
}

export async function notificar(titulo, cuerpo) {
    if (!('Notification' in window) || Notification.permission !== 'granted') {
        return;
    }

    if ('serviceWorker' in navigator) {
        const registro = await navigator.serviceWorker.ready.catch(() => null);

        if (registro) {
            await registro.showNotification(titulo, {
                body: cuerpo,
                tag: 'burrito-san-marcos',
                icon: '/icon.svg',
            }).catch(() => {});
            return;
        }
    }

    new Notification(titulo, { body: cuerpo, tag: 'burrito-san-marcos' });
}
