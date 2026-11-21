<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuración del bus interno de San Marcos
    |--------------------------------------------------------------------------
    |
    | La velocidad promedio se usa para estimar el tiempo de llegada en minutos.
    | El radio del paradero permite avisar al conductor cuando el bus está cerca.
    |
    */

    'velocidad_promedio_kmh' => (float) env('BUS_VELOCIDAD_PROMEDIO_KMH', 15),

    'radio_paradero_m' => (int) env('BUS_RADIO_PARADERO_M', 40),

    'bus_inactivo_minutos' => (int) env('BUS_INACTIVO_MINUTOS', 2),

    'limites' => [
        'latitud_min' => (float) env('BUS_LATITUD_MIN', -12.20),
        'latitud_max' => (float) env('BUS_LATITUD_MAX', -11.90),
        'longitud_min' => (float) env('BUS_LONGITUD_MIN', -77.15),
        'longitud_max' => (float) env('BUS_LONGITUD_MAX', -76.95),
    ],

];
