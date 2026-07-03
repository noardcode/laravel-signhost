<?php

use Noardcode\LaravelSignhost\Enums\Language;

return [

    /**
     * Signhost API credentials.
     *
     * @see https://www.signhost.com/integrations/rest-api-integration
     */
    'api' => [
        'url' => 'https://api.signhost.com/api',
        'user_token' => env('SIGNHOST_USER_TOKEN'),
        'app_key' => env('SIGNHOST_APP_KEY'),
    ],

    /**
     * Webhook configuration.
     */
    'webhook' => [
        'route' => env('SIGNHOST_WEBHOOK_ROUTE', 'laravel-signhost.postback'),
        'secret' => env('SIGNHOST_WEBHOOK_SECRET'),
        'token' => env('SIGNHOST_WEBHOOK_TOKEN'),
    ],

    /**
     * Configuration for the Signhost API.
     */
    'configuration' => [
        'return_url' => env('SIGNHOST_REDIRECT_URL', null),
        'reminder_interval' => 7,
        'default_verification' => 'Scribble',
        'default_language' => Language::English,
        'days_to_expire' => 90,
        'set_paragraph_on_documents' => false,
        'seal_documents' => true,
    ],

    /**
     * Disk configuration for storing documents.
     */
    'disk' => 'signhost',

    /**
     * IdProof related configuration.
     */
    'id_proof' => [
        'form_url' => env('SIGNHOST_IDPROOF_FORM_URL'),
        'checksum' => env('SIGNHOST_IDPROOF_CHECKSUM'),
    ],

    /**
     * Fake all API requests for debugging or testing.
     */
    'simulation' => [
        'enabled' => env('SIGNHOST_SIMULATION'),
        'webhooks' => [
            'id_proof' => env('SIGNHOST_SIMULATION_ID_PROOF_WEBHOOK_URL'),
            'transaction' => env('SIGNHOST_SIMULATION_TRANSACTION_WEBHOOK_URL'),
        ],
    ],
];
