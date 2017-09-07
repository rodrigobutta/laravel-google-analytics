<?php

return [

    /*
     * Nombre del proyecto en Google Developers  `https://console.developers.google.com/`.
     */
    'applicationName' => 'NOMBREEEEEE',

    /*
     * Json Auth File Path
     *
     * Despues de crear el prouecto en Googl, ir a `APIs & auth` y elegir `Credentials`, despues opcion de JSON.
     *     
     */
    'jsonFilePath' => 'storage/Project-6d62fa049680.json',

    /*
     * array de scopes      
     */
    'scopes' => [
        'https://www.googleapis.com/auth/analytics.readonly',
    ],

    'analytics' => [
        'viewId' => 'ga:' . '118363429',
    ]


];
