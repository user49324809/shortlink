<?php

return [
    'bsVersion' => '5.x',
    'shortCodeLength' => 7,
    'requestTimeout' => 8,
    'trustProxyHeaders' => filter_var(getenv('TRUST_PROXY_HEADERS') ?: 'false', FILTER_VALIDATE_BOOL),
    'adminUsername' => getenv('ADMIN_USERNAME') ?: '',
    'adminPassword' => getenv('ADMIN_PASSWORD') ?: '',
];
