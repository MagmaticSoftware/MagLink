<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Trial Settings
    |--------------------------------------------------------------------------
    |
    | Configurazione del trial gratuito per i nuovi utenti.
    |
    */

    'trial_days' => 30,

    /*
    |--------------------------------------------------------------------------
    | Subscription Plans
    |--------------------------------------------------------------------------
    |
    | Qui puoi definire i piani di abbonamento. Ogni piano ha un nome,
    | prezzo e altre informazioni rilevanti.
    |
    | NOTA: I price_id devono essere creati su Stripe Dashboard e 
    | aggiornati qui con gli ID reali.
    |
    */

    'plans' => [
        'free' => [
            'name' => 'Free',
            'description' => 'Piano gratuito per iniziare',
            'features' => [
                'Fino a 5 link brevi',
                '5 QR Code (1 dinamico)',
                '1 Landing page (max 6 blocchi)',
                'Statistiche base',
                'Supporto community',
            ],
            'limits' => [
                'links' => 5,
                'qrcodes' => 5,
                'qrcodes_dynamic' => 1,
                'pages' => 1,
                'blocks_per_page' => 6,
            ],
            'qr_customization' => [
                'colors' => true,
                'logo' => false,
                'remove_background' => false,
                'max_size' => 512,
            ],
            'monthly' => [
                'price' => 0, // gratuito
                'currency' => 'EUR',
                'price_id' => env('STRIPE_PRICE_FREE_MONTHLY', 'price_free_monthly'), // Questo sarà un piano a 0€ su Stripe o gestito internamente
            ],
        ],
        'professional' => [
            'name' => 'Professional',
            'description' => 'Perfetto per professionisti e piccole imprese',
            'popular' => true, // Badge "Più popolare"
            'features' => [
                '100 Link brevi',
                '100 QR Code (30 dinamici)',
                '5 Landing pages',
                'Statistiche avanzate',
                'Supporto email prioritario (entro 72 ore)',
            ],
            'limits' => [
                'links' => 100,
                'qrcodes' => 100,
                'qrcodes_dynamic' => 30,
                'pages' => 5,
            ],
            'qr_customization' => [
                'colors' => true,
                'logo' => true,
                'remove_background' => true,
                'max_size' => 1024,
            ],
            'monthly' => [
                'price' => 400, // 4€ in centesimi
                'currency' => 'EUR',
                'price_id' => env('STRIPE_PRICE_PROFESSIONAL_MONTHLY', 'price_professional_monthly'),
            ],
            'yearly' => [
                'price' => 4000, // 40€ in centesimi (equivale a 3.33€/mese)
                'currency' => 'EUR',
                'price_id' => env('STRIPE_PRICE_PROFESSIONAL_YEARLY', 'price_professional_yearly'),
                'discount' => 17, // percentuale di sconto rispetto al mensile
            ],
        ],
        'enterprise' => [
            'name' => 'Enterprise',
            'description' => 'Per aziende e team con esigenze avanzate',
            'features' => [
                'Tutto del piano Professional',
                'Landing pages illimitate',
                'White-label completo (logo app e consent page)',
                'Supporto email prioritario (entro 24 ore)',
            ],
            'limits' => [
                'links' => -1, // illimitato
                'qrcodes' => -1, // illimitato
                'qrcodes_dynamic' => -1, // illimitato
                'pages' => -1, // illimitato
            ],
            'qr_customization' => [
                'colors' => true,
                'logo' => true,
                'remove_background' => true,
                'max_size' => 2048,
            ],
            'monthly' => [
                'price' => 800, // 8€ in centesimi
                'currency' => 'EUR',
                'price_id' => env('STRIPE_PRICE_ENTERPRISE_MONTHLY', 'price_enterprise_monthly'),
            ],
            'yearly' => [
                'price' => 8000, // 80€ in centesimi (equivale a 6.67€/mese)
                'currency' => 'EUR',
                'price_id' => env('STRIPE_PRICE_ENTERPRISE_YEARLY', 'price_enterprise_yearly'),
                'discount' => 17, // percentuale di sconto rispetto al mensile
            ],
        ],
    ],

];
