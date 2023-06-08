<?php

return [
    'endpoint' => env('GSLINK_URI_WAYBILL'),
    'key' => env('GSLINK_KEY'),
    'iv' => env('GSLINK_IV'),
    'source' => [
        'WEB' => 1, // Mettere corrispondenza marketplace beezup con id source. WEB è il sito web
        'IBS' => 4, // Mettere corrispondenza marketplace beezup con id source. WEB è il sito web
    ],
    'source_default' => 1, // NON DEFINITO

    'payment' => [
        // Mettere corrispondenza pagamento con id pagamento (compresi quelli che ritorna beezup)
        // Mettere corrispondenza pagamento con id pagamento (compresi quelli che ritorna beezup)
        'NA' => 1, // Not available
        'CC' => 1,
    ],
    'payment_default' => 1, // NON DEFINITO
];
