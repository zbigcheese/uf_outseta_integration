<?php

return [
    'outseta' => [
        'domain'     => getenv('OUTSETA_DOMAIN') ?: '',
        'api_key'    => getenv('OUTSETA_API_KEY') ?: '',
        'secret_key' => getenv('OUTSETA_SECRET_KEY') ?: '',
    ],
];