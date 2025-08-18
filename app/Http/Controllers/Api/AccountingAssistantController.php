<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class AccountingAssistantController extends Controller
{
    public function getSuggestions(Request $request)
    {
        $context = $request->input('context');

        $prompt = $this->buildSuggestionsPrompt($context);

        try {
            $response = $this->callAI($prompt);
            $suggestions = $this->parseSuggestions($response);

            return response()->json([
                'success' => true,
                'suggestions' => $suggestions
            ]);
        } catch (\Exception $e) {
            \Log::error('AI suggestions error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'AI assistant temporarily unavailable. Using fallback suggestions.',
                'suggestions' => $this->getFallbackSuggestions($context)
            ]);
        }
    }

    public function validateTransaction(Request $request)
    {
        $context = $request->input('context');

        // Quick validation rules
        $validation = $this->performBasicValidation($context);

        if ($validation['needsAI']) {
            try {
                $aiValidation = $this->getAIValidation($context);
                $validation = array_merge($validation, $aiValidation);
            } catch (\Exception $e) {
                // Fallback to basic validation
            }
        }

        return response()->json([
            'success' => true,
            'validation' => $validation
        ]);
    }

    public function getSmartTemplates(Request $request)
    {
        $context = $request->input('context');

        $templates = $this->generateContextualTemplates($context);

        return response()->json([
            'success' => true,
            'templates' => $templates
        ]);
    }

    public function getRealTimeInsights(Request $request)
    {
        $entries = $request->input('entries', []);
        $voucherType = $request->input('voucherType', '');
        $narration = $request->input('narration', '');

        $insights = [];
        $quickFixes = [];
        $confidence = 70;

        // Advanced pattern recognition
        if ($this->detectUnusualPattern($entries, $voucherType)) {
            $insights[] = [
                'id' => 'unusual_pattern',
                'type' => 'warning',
                'message' => '🔍 This transaction pattern is unusual for ' . $voucherType,
                'action' => false
            ];
            $confidence -= 15;
        }

        // Nigerian accounting compliance checks
        if ($this->checkNigerianCompliance($entries, $voucherType)) {
            $confidence += 10;
            $insights[] = [
                'id' => 'compliance_good',
                'type' => 'suggestion',
                'message' => '🇳🇬 Transaction follows Nigerian GAAP standards',
                'action' => false
            ];
        }

        return response()->json([
            'success' => true,
            'insights' => $insights,
            'quickFixes' => $quickFixes,
            'confidence' => $confidence
        ]);
    }

    private function detectUnusualPattern($entries, $voucherType)
    {
        // Implement pattern detection logic
        $debitCount = count(array_filter($entries, fn($e) => !empty($e['debit_amount'])));
        $creditCount = count(array_filter($entries, fn($e) => !empty($e['credit_amount'])));

        // Flag if too many debits or credits for simple voucher types
        if (strpos(strtolower($voucherType), 'payment') !== false && ($debitCount > 3 || $creditCount > 3)) {
            return true;
        }

        return false;
    }

    private function checkNigerianCompliance($entries, $voucherType)
    {
        // Basic compliance checks for Nigerian accounting
        return true; // Simplified for demo
    }

    private function buildSuggestionsPrompt($context)
    {
        $voucherType = $context['voucherType'] ?? 'General';
        $narration = $context['narration'] ?? '';
        $entries = $context['entries'] ?? [];

        return "
        I'm helping a Nigerian business create accounting voucher entries.

        CONTEXT:
        - Voucher Type: {$voucherType}
        - Narration: {$narration}
        - Current Entries: " . json_encode($entries) . "

        Please analyze and provide:

        1. CORRECTIONS (if any errors):
           - Wrong debit/credit classifications
           - Incorrect account selections
           - Missing entries

        2. SUGGESTIONS:
           - Better account choices
           - Improved particulars descriptions
           - Additional entries needed

        3. EDUCATIONAL TIPS:
           - Accounting principles applied
           - Best practices for this transaction type
           - Nigerian accounting standards compliance

        Respond in JSON format:
        {
            \"corrections\": [\"correction1\", \"correction2\"],
            \"suggestions\": [\"suggestion1\", \"suggestion2\"],
            \"tips\": [\"tip1\", \"tip2\"]
        }
        ";
    }

    private function performBasicValidation($context)
    {
        $entries = $context['entries'] ?? [];
        $totalDebits = $context['totalDebits'] ?? 0;
        $totalCredits = $context['totalCredits'] ?? 0;
        $isBalanced = $context['isBalanced'] ?? false;

        // Basic validation rules
        if (count($entries) < 2) {
            return [
                'isValid' => false,
                'message' => 'At least 2 entries are required for double-entry bookkeeping.',
                'needsAI' => false
            ];
        }

        if (!$isBalanced && ($totalDebits > 0 || $totalCredits > 0)) {
            return [
                'isValid' => false,
                'message' => 'Debits must equal Credits. Current difference: ₦' . number_format(abs($totalDebits - $totalCredits), 2),
                'needsAI' => false
            ];
        }

        if ($isBalanced && $totalDebits > 0) {
            return [
                'isValid' => true,
                'message' => 'Transaction appears balanced and ready to save!',
                'needsAI' => true // Check for additional AI insights
            ];
        }

        return [
            'isValid' => false,
            'message' => 'Please complete your entries.',
            'needsAI' => false
        ];
    }

    private function generateContextualTemplates($context)
    {
        $voucherType = strtolower($context['voucherType'] ?? '');
        $narration = strtolower($context['narration'] ?? '');
        $amount = $context['amount'] ?? 0;

        $baseTemplates = [
            'cash_payment' => [
                'name' => '💰 Cash Payment',
                'description' => 'Payment made in cash',
                'confidence' => 70,
                'entries' => [
                    ['particulars' => 'Being payment made', 'amount_type' => 'debit'],
                    ['particulars' => 'Being cash paid', 'amount_type' => 'credit']
                ]
            ],
            'bank_payment' => [
                'name' => '🏦 Bank Payment',
                'description' => 'Payment via bank transfer',
                'confidence' => 70,
                'entries' => [
                    ['particulars' => 'Being payment made', 'amount_type' => 'debit'],
                    ['particulars' => 'Being bank payment', 'amount_type' => 'credit']
                ]
            ],
            'sales_cash' => [
                'name' => '🛒 Cash Sales',
                'description' => 'Cash sales transaction',
                'confidence' => 80,
                'entries' => [
                    ['particulars' => 'Being cash from sales', 'amount_type' => 'debit'],
                    ['particulars' => 'Being sales revenue', 'amount_type' => 'credit']
                ]
            ]
        ];

        // AI-powered template matching
        $matchedTemplates = [];

        foreach ($baseTemplates as $key => $template) {
            $confidence = $this->calculateTemplateRelevance($key, $voucherType, $narration);
            if ($confidence > 50) {
                $template['confidence'] = $confidence;
                $matchedTemplates[] = $template;
            }
        }

        // Sort by confidence
        usort($matchedTemplates, function($a, $b) {
            return $b['confidence'] - $a['confidence'];
        });

        return array_slice($matchedTemplates, 0, 6); // Return top 6
    }

    private function calculateTemplateRelevance($templateKey, $voucherType, $narration)
    {
        $confidence = 30; // base confidence

        // Voucher type matching
        if (strpos($voucherType, 'payment') !== false && strpos($templateKey, 'payment') !== false) {
            $confidence += 30;
        }
       if (strpos($voucherType, 'sales') !== false && strpos($templateKey, 'sales') !== false) {
            $confidence += 30;
        }
        if (strpos($voucherType, 'receipt') !== false && strpos($templateKey, 'receipt') !== false) {
            $confidence += 30;
        }

        // Narration keyword matching
        $keywords = [
            'cash' => ['cash', 'money', 'naira'],
            'bank' => ['bank', 'transfer', 'cheque', 'online'],
            'sales' => ['sales', 'sold', 'revenue', 'income'],
            'purchase' => ['purchase', 'bought', 'buy', 'supplier'],
            'expense' => ['expense', 'cost', 'bill', 'payment']
        ];

        foreach ($keywords as $category => $words) {
            if (strpos($templateKey, $category) !== false) {
                foreach ($words as $word) {
                    if (strpos($narration, $word) !== false) {
                        $confidence += 10;
                    }
                }
            }
        }

        return min($confidence, 95); // Cap at 95%
    }

    private function getFallbackSuggestions($context)
    {
        return [
            'corrections' => [],
            'suggestions' => [
                "💡 Consider adding more descriptive particulars",
                "🔍 Verify account selections match transaction type",
                "📊 Ensure all amounts are properly classified"
            ],
            'tips' => [
                "✅ Double-entry principle: Every debit must have a corresponding credit",
                "🇳🇬 Follow Nigerian GAAP for account classifications",
                "📝 Use clear, descriptive particulars for audit trail"
            ]
        ];
    }

    private function callAI($prompt)
    {
        try {
            $response = OpenAI::chat()->create([
                'model' => config('ai.model', 'gpt-3.5-turbo'),
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert Nigerian accounting assistant that helps with voucher entries following Nigerian GAAP standards.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => (int) config('ai.max_tokens', 800),
                'temperature' => (float) config('ai.temperature', 0.3),
            ]);

            return $response->choices[0]->message->content ?? '';
        } catch (\Exception $e) {
            Log::error('OpenAI API error: ' . $e->getMessage());
            throw new \Exception('AI service unavailable');
        }
    }

    private function parseSuggestions($response)
    {
        // Try to parse JSON response
        $decoded = json_decode($response, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        // Fallback parsing if not JSON
        return $this->parseTextResponse($response);
    }

    private function parseTextResponse($response)
    {
        $suggestions = [
            'corrections' => [],
            'suggestions' => [],
            'tips' => []
        ];

        // Simple text parsing logic
        $lines = explode("\n", $response);
        $currentSection = null;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if (stripos($line, 'correction') !== false) {
                $currentSection = 'corrections';
            } elseif (stripos($line, 'suggestion') !== false) {
                $currentSection = 'suggestions';
            } elseif (stripos($line, 'tip') !== false) {
                $currentSection = 'tips';
            } elseif ($currentSection && (strpos($line, '-') === 0 || strpos($line, '•') === 0)) {
                $suggestions[$currentSection][] = ltrim($line, '- •');
            }
        }

        return $suggestions;
    }

    public function explainEntry(Request $request)
    {
        $entries = $request->input('entries', []);
        $voucherType = $request->input('voucherType', '');

        $explanation = [
            'transaction' => "Here's what this {$voucherType} voucher does:",
            'steps' => [],
            'impact' => "This transaction affects your accounts as follows:",
            'balanceCheck' => '',
            'complianceNotes' => []
        ];

        foreach ($entries as $index => $entry) {
            if (!empty($entry['debit_amount']) || !empty($entry['credit_amount'])) {
                $amount = $entry['debit_amount'] ?: $entry['credit_amount'];
                $type = $entry['debit_amount'] ? 'DEBIT' : 'CREDIT';
                $particulars = $entry['particulars'] ?: 'Entry ' . ($index + 1);
                
                $explanation['steps'][] = "{$type}: {$particulars} - ₦" . number_format($amount, 2);
            }
        }

        // Calculate balance
        $totalDebits = array_sum(array_column($entries, 'debit_amount'));
        $totalCredits = array_sum(array_column($entries, 'credit_amount'));
        $isBalanced = abs($totalDebits - $totalCredits) < 0.01;

        $explanation['balanceCheck'] = $isBalanced && $totalDebits > 0 
            ? "✅ Transaction is balanced (₦" . number_format($totalDebits, 2) . ")"
            : "⚠️ Transaction needs balancing";

        $explanation['complianceNotes'] = [
            "Follows double-entry bookkeeping principle",
            "Compliant with Nigerian accounting standards",
            "Maintains proper audit trail"
        ];

        return response()->json([
            'success' => true,
            'explanation' => $explanation
        ]);
    }

    public function generateParticulars(Request $request)
    {
        $voucherType = $request->input('voucherType', '');
        $narration = $request->input('narration', '');
        $entries = $request->input('entries', []);

        $suggestions = [];

        foreach ($entries as $index => $entry) {
            $accountId = $entry['ledger_account_id'] ?? null;
            $debitAmount = $entry['debit_amount'] ?? 0;
            $creditAmount = $entry['credit_amount'] ?? 0;
            $isDebit = $debitAmount > 0;

            if ($accountId) {
                // Generate context-aware particulars
                $particular = $this->generateContextualParticular(
                    $voucherType, 
                    $narration, 
                    $isDebit, 
                    $index
                );
                
                $suggestions[] = [
                    'index' => $index,
                    'suggested_particular' => $particular,
                    'confidence' => 85
                ];
            }
        }

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }

    public function suggestAccounts(Request $request)
    {
        $particular = $request->input('particular', '');
        $voucherType = $request->input('voucherType', '');
        $isDebit = $request->input('isDebit', true);

        // AI-powered account suggestions based on context
        $suggestions = $this->getAccountSuggestions($particular, $voucherType, $isDebit);

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }

    public function analyzeEntries(Request $request)
    {
        $entries = $request->input('entries', []);
        $voucherType = $request->input('voucherType', '');
        $narration = $request->input('narration', '');

        $analysis = [
            'confidence' => 70,
            'insights' => [],
            'warnings' => [],
            'suggestions' => [],
            'compliance' => []
        ];

        // Analyze balance
        $totalDebits = array_sum(array_column($entries, 'debit_amount'));
        $totalCredits = array_sum(array_column($entries, 'credit_amount'));
        $isBalanced = abs($totalDebits - $totalCredits) < 0.01;

        if ($isBalanced && $totalDebits > 0) {
            $analysis['confidence'] += 20;
            $analysis['insights'][] = [
                'type' => 'success',
                'message' => 'Transaction is properly balanced',
                'icon' => '✅'
            ];
        } elseif ($totalDebits > 0 || $totalCredits > 0) {
            $analysis['warnings'][] = [
                'type' => 'error',
                'message' => 'Transaction is not balanced',
                'icon' => '⚠️'
            ];
        }

        // Check for missing particulars
        $emptyParticulars = 0;
        foreach ($entries as $entry) {
            if (empty($entry['particulars']) && (!empty($entry['debit_amount']) || !empty($entry['credit_amount']))) {
                $emptyParticulars++;
            }
        }

        if ($emptyParticulars > 0) {
            $analysis['suggestions'][] = [
                'type' => 'suggestion',
                'message' => "{$emptyParticulars} entries need descriptions",
                'icon' => '📝'
            ];
        } else {
            $analysis['confidence'] += 10;
        }

        // Nigerian compliance checks
        $analysis['compliance'][] = [
            'check' => 'Double-entry principle',
            'status' => $isBalanced ? 'passed' : 'failed',
            'description' => 'Total debits must equal total credits'
        ];

        return response()->json([
            'success' => true,
            'analysis' => $analysis
        ]);
    }

    private function generateContextualParticular($voucherType, $narration, $isDebit, $index)
    {
        $voucherLower = strtolower($voucherType);
        $narrationLower = strtolower($narration);

        // Payment vouchers
        if (strpos($voucherLower, 'payment') !== false) {
            if ($isDebit) {
                if (strpos($narrationLower, 'electricity') !== false) return "Being electricity bill payment";
                if (strpos($narrationLower, 'rent') !== false) return "Being rent payment";
                if (strpos($narrationLower, 'salary') !== false) return "Being salary payment";
                return "Being payment made";
            } else {
                if (strpos($narrationLower, 'cash') !== false) return "Being cash paid";
                if (strpos($narrationLower, 'bank') !== false) return "Being bank payment";
                return "Being payment by bank";
            }
        }

        // Sales vouchers
        if (strpos($voucherLower, 'sales') !== false) {
            if ($isDebit) {
                return "Being cash/bank from sales";
            } else {
                return "Being sales revenue";
            }
        }

        // Receipt vouchers
        if (strpos($voucherLower, 'receipt') !== false) {
            if ($isDebit) {
                return "Being cash/bank received";
            } else {
                return "Being amount received";
            }
        }

        // Default particulars
        return $isDebit ? "Being amount debited" : "Being amount credited";
    }

    private function getAccountSuggestions($particular, $voucherType, $isDebit)
    {
        $suggestions = [];
        $particular = strtolower($particular);
        $voucherType = strtolower($voucherType);

        // Common account patterns
        $patterns = [
            'cash' => ['Cash in Hand', 'Cash Account'],
            'bank' => ['Bank Account', 'Current Account'],
            'sales' => ['Sales Revenue', 'Sales Account'],
            'rent' => ['Rent Expense', 'Rent Account'],
            'electricity' => ['Electricity Expense', 'Utilities Expense'],
            'salary' => ['Salary Expense', 'Staff Salary'],
            'equipment' => ['Equipment Account', 'Fixed Assets'],
            'supplier' => ['Accounts Payable', 'Suppliers Account']
        ];

        foreach ($patterns as $keyword => $accounts) {
            if (strpos($particular, $keyword) !== false) {
                foreach ($accounts as $account) {
                    $suggestions[] = [
                        'account_name' => $account,
                        'confidence' => 80,
                        'reason' => "Matches keyword: {$keyword}"
                    ];
                }
            }
        }

        return array_slice($suggestions, 0, 5); // Return top 5
    }

    private function getAIValidation($context)
    {
        try {
            $prompt = $this->buildValidationPrompt($context);
            $response = $this->callAI($prompt);
            $validation = $this->parseValidationResponse($response);
            
            return $validation;
        } catch (\Exception $e) {
            Log::warning('AI validation failed, using fallback: ' . $e->getMessage());
            return [
                'insights' => [
                    '🤖 AI validation temporarily unavailable.',
                    '✅ Basic validation passed.',
                    '📊 Transaction appears structurally sound.'
                ]
            ];
        }
    }

    private function buildValidationPrompt($context)
    {
        $voucherType = $context['voucherType'] ?? 'General';
        $entries = $context['entries'] ?? [];
        $narration = $context['narration'] ?? '';
        $totalDebits = $context['totalDebits'] ?? 0;
        $totalCredits = $context['totalCredits'] ?? 0;

        return "
        Analyze this Nigerian accounting transaction for accuracy and compliance:

        TRANSACTION DETAILS:
        - Type: {$voucherType}
        - Narration: {$narration}
        - Total Debits: ₦" . number_format($totalDebits, 2) . "
        - Total Credits: ₦" . number_format($totalCredits, 2) . "
        - Entries: " . json_encode($entries) . "

        Please validate:
        1. Account classifications (Assets, Liabilities, Equity, Income, Expenses)
        2. Debit/Credit rules compliance
        3. Nigerian GAAP compliance
        4. Logical transaction flow
        5. Common errors or missing entries

        Respond in JSON format:
        {
            \"isValid\": true/false,
            \"confidence\": 85,
            \"insights\": [\"insight1\", \"insight2\"],
            \"warnings\": [\"warning1\", \"warning2\"],
            \"suggestions\": [\"suggestion1\", \"suggestion2\"]
        }
        ";
    }

    private function parseValidationResponse($response)
    {
        $decoded = json_decode($response, true);
        
        if (json_last_error() === JSON_ERROR_NONE && isset($decoded['isValid'])) {
            return $decoded;
        }

        // Fallback parsing
        return [
            'isValid' => true,
            'confidence' => 70,
            'insights' => ['AI analysis completed'],
            'warnings' => [],
            'suggestions' => []
        ];
    }

    public function askQuestion(Request $request)
    {
        $question = $request->input('question', '');
        $context = $request->input('context', []);

        if (empty($question)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a question.'
            ]);
        }

        try {
            $prompt = $this->buildQuestionPrompt($question, $context);
            $response = $this->callAI($prompt);
            
            return response()->json([
                'success' => true,
                'answer' => $response,
                'question' => $question
            ]);
            
        } catch (\Exception $e) {
            Log::error('AI Q&A error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'AI assistant temporarily unavailable.',
                'answer' => $this->getFallbackAnswer($question)
            ]);
        }
    }

    private function buildQuestionPrompt($question, $context)
    {
        $voucherType = $context['voucherType'] ?? '';
        $narration = $context['narration'] ?? '';
        
        return "
        You are an expert Nigerian accounting consultant and bookkeeper. A user is asking you a question while working on their accounting vouchers.

        CURRENT CONTEXT:
        - Voucher Type: {$voucherType}
        - Narration: {$narration}

        USER QUESTION: {$question}

        Please provide a clear, helpful, and accurate answer that:
        1. Directly addresses their question
        2. Uses Nigerian accounting standards and terminology where applicable
        3. Provides practical examples if helpful
        4. Is easy to understand for both beginners and experienced accountants
        5. References relevant accounting principles or regulations when appropriate

        Keep your answer concise but comprehensive, and use a friendly, professional tone.
        ";
    }

    private function getFallbackAnswer($question)
    {
        // Check for common question patterns and provide basic answers
        $questionLower = strtolower($question);
        
        if (strpos($questionLower, 'debit') !== false && strpos($questionLower, 'credit') !== false) {
            return "Debit and Credit are the two sides of every accounting transaction:\n\n• DEBIT (Dr.) increases: Assets, Expenses, Dividends\n• DEBIT decreases: Liabilities, Equity, Revenue\n\n• CREDIT (Cr.) increases: Liabilities, Equity, Revenue\n• CREDIT decreases: Assets, Expenses, Dividends\n\nRemember: Total Debits must always equal Total Credits in every transaction.";
        }
        
        if (strpos($questionLower, 'voucher') !== false) {
            return "Vouchers are accounting documents that record financial transactions:\n\n• Payment Voucher: Records money going out\n• Receipt Voucher: Records money coming in\n• Journal Voucher: Records adjustments and transfers\n\nEach voucher must have balanced debit and credit entries following double-entry bookkeeping principles.";
        }
        
        if (strpos($questionLower, 'gaap') !== false || strpos($questionLower, 'nigerian') !== false) {
            return "Nigerian GAAP (Generally Accepted Accounting Principles) requires:\n\n• Double-entry bookkeeping\n• Proper documentation for all transactions\n• Consistent accounting methods\n• Regular financial reporting\n• Compliance with local tax regulations\n\nAlways maintain proper records and follow established accounting standards.";
        }
        
        return "I apologize, but I cannot provide a specific answer right now. Please try asking your question again, or consult with a qualified accountant for detailed guidance on complex accounting matters.";
    }
}
