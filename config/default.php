<?php

return [
    'outseta' => [
        'domain'     => getenv('OUTSETA_DOMAIN') ?: '',
        'api_key'    => getenv('OUTSETA_API_KEY') ?: '',
        'secret_key' => getenv('OUTSETA_SECRET_KEY') ?: '',
        'webhook_key' => getenv('OUTSETA_WEBHOOK_KEY') ?: '',
    ],
    'csrf' => [
        'blacklist' => [
            '^/api/outseta/webhooks/*' => [
                'POST'
            ]
        ]
    ],
];