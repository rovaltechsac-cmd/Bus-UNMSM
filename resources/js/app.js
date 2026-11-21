const pagina = document.body.dataset.pagina;

if (pagina === 'admin') {
    import('./admin.js').catch((error) => console.error('No se pudo cargar el panel del administrador', error));
} else if (pagina === 'conductor') {
    import('./conductor.js').catch((error) => console.error('No se pudo cargar el panel del conductor', error));
} else if (pagina === 'pasajero') {
    import('./pasajero.js').catch((error) => console.error('No se pudo cargar la vista del pasajero', error));
}
