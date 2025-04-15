<?php

return [
    'name' => 'Forms',
    
    // Options for default form settings
    'defaults' => [
        'collect_email' => true,
        'is_public' => true,
        'expires_after_days' => 30, // Default expiration in days from creation, null = no expiration
    ],
    
    // Options for form responses
    'responses' => [
        'limit_per_user' => null, // Set to a number to limit responses per user, null = no limit
        'allow_anonymous' => true, // Allow responses without login
    ],
    
    // Security settings
    'security' => [
        'rate_limit' => [ // Rate limiting for form submissions
            'enabled' => true,
            'max_attempts' => 5,
            'decay_minutes' => 1,
        ],
    ],
];