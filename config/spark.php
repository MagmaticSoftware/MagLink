<?php

use App\Models\Company;
use App\Models\User;

return [

    /*
    |--------------------------------------------------------------------------
    | Spark Path
    |--------------------------------------------------------------------------
    |
    | This configuration option determines the URI at which the Spark billing
    | portal is available. You are free to change this URI to a value that
    | you prefer. You shall link to this location from your application.
    |
    */

    'path' => 'billing',

    /*
    |--------------------------------------------------------------------------
    | Spark Middleware
    |--------------------------------------------------------------------------
    |
    | These are the middleware that requests to the Spark billing portal must
    | pass through before being accepted. Typically, the default list that
    | is defined below should be suitable for most Laravel applications.
    |
    */

    'middleware' => ['web', 'auth'],

    /*
    |--------------------------------------------------------------------------
    | Branding
    |--------------------------------------------------------------------------
    |
    | These configuration values allow you to customize the branding of the
    | billing portal, including the primary color and the logo that will
    | be displayed within the billing portal. This logo value must be
    | the absolute path to an SVG logo within the local filesystem.
    |
    */

    'brand' =>  [
        'logo' => realpath(__DIR__.'/../public/svg/billing-logo.svg'),
        'color' => 'bg-gray-800',
    ],

    /*
    |--------------------------------------------------------------------------
    | Proration Behavior
    |--------------------------------------------------------------------------
    |
    | This value determines if charges are prorated when making adjustments
    | to a plan such as incrementing or decrementing the quantity of the
    | plan. This also determines proration behavior if changing plans.
    |
    */

    'prorates' => true,

    /*
    |--------------------------------------------------------------------------
    | Spark Date Format
    |--------------------------------------------------------------------------
    |
    | This date format will be utilized by Spark to format dates in various
    | locations within the billing portal, such as while showing invoice
    | dates. You should customize the format based on your own locale.
    |
    */

    'date_format' => 'F j, Y',

    /*
    |--------------------------------------------------------------------------
    | Spark Billables
    |--------------------------------------------------------------------------
    |
    | Below you may define billable entities supported by your Spark driven
    | application. The Paddle edition of Spark currently only supports a
    | single billable model entity (team, user, etc.) per application.
    |
    | In addition to defining your billable entity, you may also define its
    | plans and the plan's features, including a short description of it
    | as well as a "bullet point" listing of its distinctive features.
    |
    */

    'billables' => [

        'user' => [
            'model' => Company::class,

            'trial_days' => 15,

            'default_interval' => 'monthly',

            'plans' => [
                [
                    'name' => 'Free',
                    'short_description' => 'Ideale per iniziare gratuitamente.',
                    'monthly_id' => env('SPARK_FREE_PLAN', 'pri_free_monthly'),
                    'features' => [
                        '1 QR Code dinamico',
                        '10 link accorciati',
                        'Pagina pubblica semplice',
                    ],
                    'archived' => false,
                ],
                [
                    'name' => 'Starter',
                    'short_description' => 'Funzionalità base per piccoli progetti.',
                    'monthly_id' => env('SPARK_STARTER_MONTHLY_PLAN', 'pri_starter_monthly'),
                    'yearly_id' => env('SPARK_STARTER_YEARLY_PLAN', 'pri_starter_yearly'),
                    'yearly_incentive' => "Risparmia 10€",
                    'features' => [
                        '10 QR Code dinamici',
                        '100 link accorciati',
                        'Personalizzazione della pagina',
                    ],
                    'archived' => false,
                ],
                [
                    'name' => 'Pro',
                    'short_description' => 'Per professionisti e aziende.',
                    'monthly_id' => env('SPARK_PRO_MONTHLY_PLAN', 'pri_pro_monthly'),
                    'yearly_id' => env('SPARK_PRO_YEARLY_PLAN', 'pri_pro_yearly'),
                    'yearly_incentive' => "Risparmia 20€",
                    'features' => [
                        '100 QR Code',
                        '1.000 link',
                        'Editor avanzato',
                        'Supporto prioritario',
                    ],
                    'archived' => false,
                ],
                [
                    'name' => 'Enterprise',
                    'short_description' => 'Soluzione completa per team e grandi aziende.',
                    'monthly_id' => env('SPARK_ENTERPRISE_MONTHLY_PLAN', 'pri_enterprise_monthly'),
                    'yearly_id' => env('SPARK_ENTERPRISE_YEARLY_PLAN', 'pri_enterprise_yearly'),
                    'yearly_incentive' => "Risparmia 40€",
                    'features' => [
                        'QR Code e link illimitati',
                        'Team access',
                        'Supporto tecnico diretto',
                        'Statistiche avanzate',
                    ],
                    'archived' => false,
                ],
            ],

        ],

    ],
];
