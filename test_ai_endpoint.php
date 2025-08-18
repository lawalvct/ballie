<?php

require_once 'vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client();

// Test data
$testData = [
    'context' => [
        'voucherType' => 'Payment Voucher',
        'narration' => 'Payment for office rent',
        'entries' => [
            [
                'particulars' => 'Being rent payment',
                'debit_amount' => 50000,
                'credit_amount' => 0
            ],
            [
                'particulars' => 'Being bank payment',
                'debit_amount' => 0,
                'credit_amount' => 50000
            ]
        ],
        'totalDebits' => 50000,
        'totalCredits' => 50000,
        'isBalanced' => true
    ]
];

try {
    echo "Testing AI Suggestions endpoint...\n";
    
    // You'll need to replace this URL with your actual Laravel app URL
    $response = $client->post('http://127.0.0.1:8000/api/ai/accounting-suggestions', [
        'json' => $testData,
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]
    ]);
    
    $result = json_decode($response->getBody(), true);
    
    echo "Response: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
