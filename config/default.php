<?php

return [
    'outseta' => [
        'domain'     => 'stout-ux',
        'api_key'    => getenv('OUTSETA_API_KEY') ?: '',
        'secret_key' => getenv('OUTSETA_SECRET_KEY') ?: '',
    ],
];