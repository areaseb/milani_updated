<?php

// AGGIORNARE ANCHE CONFIG/GSLINK.PHP !!!

return [
    'store_id' => env('BEEZUP_STORE_ID', ''),
    'api_key' => env('BEEZUP_API_KEY', ''),
    'carriers' => [
        'Maison du Monde' => 3, // 1 = Non definito, 2 = SDA, 3 = BRT
        'Mano mano' => 3, // 1 = Non definito, 2 = SDA, 3 = BRT
        'Leroy Merlin' => 3, // 1 = Non definito, 2 = SDA, 3 = BRT
        'AMAZON-ITA' => 3,
        'LEROYMERLIN_ITA' => 3,
        'MONECHELLE' => 3,
        'MAISONDUMONDE_ITA' => 3,
        'EBAY-ITA' => 3,
    ],
    'carriers_name' => [
        1 => 'Non definito',
        2 => 'SDA',
        3 => 'BRT',
    ],
    'sources' => [
        'AMAZON-ITA' => 'Amazon.it',
        'LEROYMERLIN_ITA' => 'LeroyMerlin.it',
        'MONECHELLE' => 'ManoMano.it',
        'MAISONDUMONDE_ITA' => 'MaisonDuMonde.it',
        'EBAY-ITA' => 'Ebay.it'
    ],
    'notification_email' => env('BEEZUP_NOTIFICATION_EMAIL', ''),
];
