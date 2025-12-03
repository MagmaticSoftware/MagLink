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
                'Fino a 10 link brevi',
                '5 QR Code statici',
                '1 Landing page',
                'Statistiche base',
                'Supporto community',
            ],
            'limits' => [
                'links' => 10,
                'qrcodes' => 5,
                'pages' => 1,
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
                'Link illimitati',
                'QR Code illimitati',
                '10 Landing pages',
                'QR Code dinamici',
                'Statistiche avanzate',
                'Custom domain',
                'Supporto email prioritario',
                'Rimozione branding',
            ],
            'limits' => [
                'links' => -1, // illimitato
                'qrcodes' => -1, // illimitato
                'pages' => 10,
            ],
            'monthly' => [
                'price' => 900, // 9€ in centesimi
                'currency' => 'EUR',
                'price_id' => env('STRIPE_PRICE_PROFESSIONAL_MONTHLY', 'price_professional_monthly'),
            ],
            'yearly' => [
                'price' => 9000, // 90€ in centesimi (equivale a 7.50€/mese)
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
                'API Access completo',
                'Team multi-utente',
                'White-label completo',
                'Supporto telefonico dedicato',
                'Account manager dedicato',
                'SLA garantito',
                'Integrazioni custom',
            ],
            'limits' => [
                'links' => -1, // illimitato
                'qrcodes' => -1, // illimitato
                'pages' => -1, // illimitato
            ],
            'monthly' => [
                'price' => 2900, // 29€ in centesimi
                'currency' => 'EUR',
                'price_id' => env('STRIPE_PRICE_ENTERPRISE_MONTHLY', 'price_enterprise_monthly'),
            ],
            'yearly' => [
                'price' => 29000, // 290€ in centesimi (equivale a ~24€/mese)
                'currency' => 'EUR',
                'price_id' => env('STRIPE_PRICE_ENTERPRISE_YEARLY', 'price_enterprise_yearly'),
                'discount' => 17, // percentuale di sconto rispetto al mensile
            ],
        ],
    ],

];
