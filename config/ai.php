<?php

return [
    'enabled' => env('AI_ENABLED', false),
    'provider' => env('AI_PROVIDER', 'openai'), // openai, anthropic, local
    'endpoint' => env('AI_ENDPOINT'),
    'api_key' => env('AI_API_KEY'),
    'model' => env('AI_MODEL', 'gpt-3.5-turbo'),
    'max_tokens' => env('AI_MAX_TOKENS', 800),
    'temperature' => env('AI_TEMPERATURE', 0.3),

    'accounting' => [
        'context_window' => 4000,
        'confidence_threshold' => 70,
        'cache_duration' => 3600, // 1 hour
    ],

    'rate_limiting' => [
        'requests_per_minute' => 10,
        'requests_per_hour' => 100,
    ]
];
