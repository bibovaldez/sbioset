<?php

return [
    'admin' => [
        'email' => env('ADMIN_EMAIL', 'admin@example.com'),
        'password' => env('ADMIN_PASSWORD', 'defaultPassword'),
    ],
    
    'bcrypt_rounds' => env('BCRYPT_ROUNDS', 12),
    
    'session' => [
        'lifetime' => env('SESSION_LIFETIME', 120),
        'encrypt' => env('SESSION_ENCRYPT', false),
        'secure_cookie' => env('SESSION_SECURE_COOKIE', true),
    ],
    
    'backup' => [
        'archive_password' => env('BACKUP_ARCHIVE_PASSWORD', ''),
    ],
    
    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY', ''),
        'secret_key' => env('RECAPTCHA_SECRET_KEY', ''),
    ],
    
    'roboflow' => [
        'api_key' => env('ROBOFLOW_API_KEY', ''),
        'api_url' => env('ROBOFLOW_API_URL', ''),
    ],
    
    'log_viewer' => [
        'enabled' => env('LOG_VIEWER_ENABLED', true),
        'api_only' => env('LOG_VIEWER_API_ONLY', false),
        'stateful_domains' => env('LOG_VIEWER_API_STATEFUL_DOMAINS', ['localhost']),
    ],

    'csp' => [
        'enabled' => env('CSP_ENABLED', true),
    ],
    'encryption' => [
        'key' => env('ENCRYPTION_KEY', ''),
        'additional_data' => env('ENCRYPTION_ADDITIONAL_DATA', ''),
    ],
];
