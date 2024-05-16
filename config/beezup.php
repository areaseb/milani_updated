<?php

return [
    'store_id' => env('BEEZUP_STORE_ID', ''),
    'api_key' => env('BEEZUP_API_KEY', ''),
    'carriers' => [
        'Maison du Monde' => 3, // 1 = Non definito, 2 = SDA, 3 = BRT
        'Mano mano' => 3, // 1 = Non definito, 2 = SDA, 3 = BRT
        'Leroy Merlin' => 3, // 1 = Non definito, 2 = SDA, 3 = BRT
        'AMAZON-ITA' => 3,
    ],
    'carriers_name' => [
        1 => 'Non definito',
        2 => 'SDA',
        3 => 'BRT',
    ],
    'notification_email' => env('BEEZUP_NOTIFICATION_EMAIL', ''),
];
