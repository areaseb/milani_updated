<?php

return [
    'endpoint' => env('GSLINK_URI_WAYBILL'),
    'key' => env('GSLINK_KEY'),
    'iv' => env('GSLINK_IV'),
    'source' => [
        'WEB' => 18, // Mettere corrispondenza marketplace beezup con id source. WEB è il sito web
        'IBS' => 4, // Mettere corrispondenza marketplace beezup con id source. WEB è il sito web
        'Amazon' => 2,
        'LeroyMerlin' => 12,
        'MaisonDuMonde' => 14,
        'ManoMano' => 6,
        'Ebay' => 17,
        'E-Price' => 9
    ],
    'source_default' => 1, // NON DEFINITO

    'payment' => [
        // Mettere corrispondenza pagamento con id pagamento (compresi quelli che ritorna beezup)
        // Mettere corrispondenza pagamento con id pagamento (compresi quelli che ritorna beezup)
        'NA' => 1, // Not available
        'CC' => 7,
        'Other' => 7,
        'multisafepay' => 21,
        'paypal' => 9,
        'bank_transfer' => 10,
        'cod' => 11
        
    ],
    'payment_default' => 1, // NON DEFINITO
];
