<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Privacy Policy Version
    |--------------------------------------------------------------------------
    |
    | Current version of the privacy policy. Increment this whenever you
    | update the privacy text. Format: MAJOR.MINOR (e.g., 1.0, 1.1, 2.0)
    |
    */
    'version' => '1.0',

    /*
    |--------------------------------------------------------------------------
    | Privacy Policy Text
    |--------------------------------------------------------------------------
    |
    | The exact text shown to users when they need to give consent.
    | This is saved in the database for legal proof.
    |
    */
    'text' => [
        'it' => 'Questo link raccoglie dati statistici anonimi per finalità di analisi (indirizzo IP, browser, paese, tipo di dispositivo). I dati raccolti sono utilizzati esclusivamente per generare statistiche aggregate e non permettono di identificarti personalmente. Cliccando su "Accetta e continua" acconsenti alla raccolta di questi dati nel pieno rispetto del GDPR (Regolamento UE 2016/679).',
        
        'en' => 'This link collects anonymous statistical data for analytics purposes (IP address, browser, country, device type). The collected data is used exclusively to generate aggregate statistics and does not allow personal identification. By clicking "Accept and continue" you consent to the collection of this data in full compliance with GDPR (EU Regulation 2016/679).',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Language
    |--------------------------------------------------------------------------
    |
    | Default language for privacy policy text.
    |
    */
    'default_language' => 'it',

    /*
    |--------------------------------------------------------------------------
    | Changelog
    |--------------------------------------------------------------------------
    |
    | Privacy policy version history for reference.
    |
    */
    'changelog' => [
        '1.0' => [
            'date' => '2026-01-30',
            'changes' => 'Initial privacy policy for GDPR-compliant tracking',
        ],
    ],
];
