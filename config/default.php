<?php

return [
    'outseta' => [
        'domain'     => getenv('OUTSETA_DOMAIN') ?: '',
        'api_key'    => getenv('OUTSETA_API_KEY') ?: '',
        'secret_key' => getenv('OUTSETA_SECRET_KEY') ?: '',
        'webhook_key' => 'B362877F36D36B7FC7FD4495C49024860C5F9D07A0DBEF1403F61FF7124CDC86',
    ],
    'csrf' => [
        'blacklist' => [
            '^/api/outseta/webhooks' => [
                'POST'
            ]
        ]
    ],
];