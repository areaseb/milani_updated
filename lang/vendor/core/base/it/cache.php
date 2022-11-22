<?php

return [
    'cache_commands' => 'Cancella i comandi della cache',
    'cache_management' => 'Gestione della cache',
    'commands' => [
        'clear_cms_cache' => [
            'description' => 'Clear CMS caching: database caching, static blocks... Run this command when you don\'t see the changes after updating data.',
            'success_msg' => 'Cache pulita',
            'title' => 'Cancella tutta la cache del CMS',
        ],
        'clear_config_cache' => [
            'description' => 'Potrebbe essere necessario aggiornare la cache di configurazione quando si modifica qualcosa nell\'ambiente di produzione.',
            'success_msg' => 'Cache di configurazione pulita',
            'title' => 'Svuota la cache di configurazione',
        ],
        'clear_log' => [
            'description' => 'Cancella i file di registro del sistema',
            'success_msg' => 'Il registro di sistema è stato pulito',
            'title' => 'Pulisci il registro',
        ],
        'clear_route_cache' => [
            'description' => 'Cancella il percorso della cache.',
            'success_msg' => 'La cache del percorso è stata pulita',
            'title' => 'Cancella la cache del percorso',
        ],
        'refresh_compiled_views' => [
            'description' => 'Cancella le visualizzazioni compilate per aggiornare le visualizzazioni.',
            'success_msg' => 'Visualizzazione della cache aggiornata',
            'title' => 'Aggiorna le visualizzazioni compilate',
        ],
    ],
];
