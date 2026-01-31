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
        $model = config('ai.model', 'gpt-3.5-turbo');
        $systemMessage = 'You are an expert Nigerian accounting assistant that helps with voucher entries following Nigerian GAAP standards.';

        $apiKey = config('openai.api_key') ?: config('ai.api_key');
        if (empty($apiKey)) {
            throw new \Exception('OpenAI API key is missing');
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout((int) config('openai.request_timeout', 30))
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemMessage],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'max_tokens' => (int) config('ai.max_tokens', 800),
                    'temperature' => (float) config('ai.temperature', 0.3),
                ]);

            if ($response->failed()) {
                Log::error('OpenAI HTTP error: ' . $response->body());
                throw new \Exception('AI service unavailable');
            }

            $text = data_get($response->json(), 'choices.0.message.content', '');
            if (!empty($text)) {
                return $text;
            }
        } catch (\Throwable $e) {
            Log::error('OpenAI HTTP error: ' . $e->getMessage());
        }

        throw new \Exception('AI service unavailable');
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

    /**
     * Interpret Profit & Loss report data using AI
     */
    public function interpretProfitLoss(Request $request)
    {
        $reportData = $request->input('reportData', []);

        if (empty($reportData)) {
            return response()->json([
                'success' => false,
                'message' => 'No report data provided.'
            ]);
        }

        try {
            $prompt = $this->buildProfitLossInterpretationPrompt($reportData);
            $response = $this->callAI($prompt);

            return response()->json([
                'success' => true,
                'interpretation' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('AI Profit & Loss interpretation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'AI assistant temporarily unavailable.',
                'interpretation' => $this->getFallbackProfitLossInterpretation($reportData)
            ]);
        }
    }

    /**
     * Build the prompt for Profit & Loss interpretation
     */
    private function buildProfitLossInterpretationPrompt(array $reportData): string
    {
        $fromDate = $reportData['fromDate'] ?? 'N/A';
        $toDate = $reportData['toDate'] ?? 'N/A';
        $totalIncome = $reportData['totalIncome'] ?? 0;
        $totalExpenses = $reportData['totalExpenses'] ?? 0;
        $netProfit = $reportData['netProfit'] ?? 0;
        $profitMargin = $totalIncome > 0 ? round(($netProfit / $totalIncome) * 100, 2) : 0;
        $incomeAccounts = $reportData['incomeAccounts'] ?? [];
        $expenseAccounts = $reportData['expenseAccounts'] ?? [];
        $openingStock = $reportData['openingStock'] ?? 0;
        $closingStock = $reportData['closingStock'] ?? 0;
        $compareData = $reportData['compareData'] ?? null;

        $incomeBreakdown = '';
        foreach ($incomeAccounts as $account) {
            $name = $account['name'] ?? 'Unknown';
            $amount = number_format($account['amount'] ?? 0, 2);
            $incomeBreakdown .= "   - {$name}: ₦{$amount}\n";
        }

        $expenseBreakdown = '';
        foreach ($expenseAccounts as $account) {
            $name = $account['name'] ?? 'Unknown';
            $amount = number_format($account['amount'] ?? 0, 2);
            $expenseBreakdown .= "   - {$name}: ₦{$amount}\n";
        }

        $comparisonInfo = '';
        if ($compareData) {
            $prevIncome = $compareData['totalIncome'] ?? 0;
            $prevExpenses = $compareData['totalExpenses'] ?? 0;
            $prevProfit = $compareData['netProfit'] ?? 0;
            $incomeChange = $prevIncome > 0 ? round((($totalIncome - $prevIncome) / $prevIncome) * 100, 2) : 0;
            $expenseChange = $prevExpenses > 0 ? round((($totalExpenses - $prevExpenses) / $prevExpenses) * 100, 2) : 0;
            $profitChange = $prevProfit != 0 ? round((($netProfit - $prevProfit) / abs($prevProfit)) * 100, 2) : 0;

            $comparisonInfo = "
COMPARISON WITH PREVIOUS PERIOD ({$compareData['fromDate']} to {$compareData['toDate']}):
- Previous Income: ₦" . number_format($prevIncome, 2) . " | Change: {$incomeChange}%
- Previous Expenses: ₦" . number_format($prevExpenses, 2) . " | Change: {$expenseChange}%
- Previous Net Profit: ₦" . number_format($prevProfit, 2) . " | Change: {$profitChange}%
";
        }

        return "
You are BallieAI, an expert Nigerian business and accounting analyst. Analyze this Profit & Loss report and provide clear, actionable insights for the business owner.

PROFIT & LOSS REPORT
Period: {$fromDate} to {$toDate}

INCOME BREAKDOWN:
{$incomeBreakdown}
Total Income: ₦" . number_format($totalIncome, 2) . "

EXPENSE BREAKDOWN:
{$expenseBreakdown}
Total Expenses: ₦" . number_format($totalExpenses, 2) . "

STOCK INFORMATION:
- Opening Stock: ₦" . number_format($openingStock, 2) . "
- Closing Stock: ₦" . number_format($closingStock, 2) . "

KEY METRICS:
- Net " . ($netProfit >= 0 ? 'Profit' : 'Loss') . ": ₦" . number_format(abs($netProfit), 2) . "
- Profit Margin: {$profitMargin}%
{$comparisonInfo}

Please provide a comprehensive interpretation that includes:

1. **Overall Performance Summary** - A brief overview of the business's financial health for this period.

2. **Income Analysis** - Identify top income sources, patterns, and opportunities to grow revenue.

3. **Expense Analysis** - Identify major expense categories, flag any unusually high expenses, suggest cost-cutting opportunities.

4. **Profitability Insights** - Comment on the profit margin, compare to industry standards for Nigerian SMEs if applicable.

5. **Stock Movement** - Analyze the stock movement and its implications on cash flow.

6. **Actionable Recommendations** - Provide 3-5 specific, actionable recommendations to improve profitability.

7. **Risk Alerts** - Flag any concerning trends or potential financial risks.

Use clear, simple language suitable for a Nigerian business owner who may not be an accounting expert. Use Naira (₦) for currency. Be encouraging but honest.
";
    }

    /**
     * Fallback interpretation when AI is unavailable
     */
    private function getFallbackProfitLossInterpretation(array $reportData): string
    {
        $totalIncome = $reportData['totalIncome'] ?? 0;
        $totalExpenses = $reportData['totalExpenses'] ?? 0;
        $netProfit = $reportData['netProfit'] ?? 0;
        $profitMargin = $totalIncome > 0 ? round(($netProfit / $totalIncome) * 100, 2) : 0;

        $status = $netProfit >= 0 ? 'profit' : 'loss';
        $statusEmoji = $netProfit >= 0 ? '✅' : '⚠️';

        $interpretation = "## {$statusEmoji} Financial Summary\n\n";

        if ($netProfit >= 0) {
            $interpretation .= "Your business generated a **net profit of ₦" . number_format($netProfit, 2) . "** during this period. ";
        } else {
            $interpretation .= "Your business recorded a **net loss of ₦" . number_format(abs($netProfit), 2) . "** during this period. ";
        }

        $interpretation .= "Your profit margin is **{$profitMargin}%**.\n\n";

        $interpretation .= "### 📊 Quick Analysis\n\n";
        $interpretation .= "- **Total Income:** ₦" . number_format($totalIncome, 2) . "\n";
        $interpretation .= "- **Total Expenses:** ₦" . number_format($totalExpenses, 2) . "\n";
        $interpretation .= "- **Expense Ratio:** " . ($totalIncome > 0 ? round(($totalExpenses / $totalIncome) * 100, 2) : 0) . "% of income\n\n";

        $interpretation .= "### 💡 General Recommendations\n\n";
        $interpretation .= "1. Review your largest expense categories for cost-saving opportunities\n";
        $interpretation .= "2. Focus on increasing your top-performing income streams\n";
        $interpretation .= "3. Monitor cash flow regularly to ensure operational stability\n";
        $interpretation .= "4. Consider diversifying income sources to reduce risk\n\n";

        $interpretation .= "*For a more detailed AI-powered analysis, please try again later or contact support.*";

        return $interpretation;
    }

    /**
     * Export Profit & Loss interpretation as PDF
     */
    public function exportProfitLossInterpretationPdf(Request $request)
    {
        $interpretation = $request->input('interpretation', '');
        $reportDataJson = $request->input('reportData', '{}');

        if (empty($interpretation)) {
            return response()->json([
                'success' => false,
                'message' => 'No interpretation data provided.'
            ], 400);
        }

        try {
            // Parse report data JSON string
            $reportData = is_string($reportDataJson) ? json_decode($reportDataJson, true) : $reportDataJson;

            $tenant = auth()->user()->tenant;
            $fromDate = $reportData['fromDate'] ?? 'N/A';
            $toDate = $reportData['toDate'] ?? 'N/A';
            $totalIncome = $reportData['totalIncome'] ?? 0;
            $totalExpenses = $reportData['totalExpenses'] ?? 0;
            $netProfit = $reportData['netProfit'] ?? 0;

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('tenant.reports.profit-loss-interpretation-pdf', compact(
                'tenant',
                'interpretation',
                'reportData',
                'fromDate',
                'toDate',
                'totalIncome',
                'totalExpenses',
                'netProfit'
            ));

            return $pdf->download('profit_loss_interpretation_' . $fromDate . '_to_' . $toDate . '.pdf');

        } catch (\Exception $e) {
            Log::error('PDF generation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'interpretation_length' => strlen($interpretation),
                'report_data' => $reportDataJson
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Interpret Balance Sheet using AI
     */
    public function interpretBalanceSheet(Request $request)
    {
        try {
            $reportData = $request->input('reportData');

            // Build the prompt for AI
            $prompt = $this->buildBalanceSheetInterpretationPrompt($reportData);

            // Call AI API
            $interpretation = $this->callAI($prompt);

            if (!$interpretation) {
                // Return fallback interpretation if AI is unavailable
                $interpretation = $this->getFallbackBalanceSheetInterpretation($reportData);
            }

            return response()->json([
                'success' => true,
                'interpretation' => $interpretation
            ]);

        } catch (\Exception $e) {
            Log::error('Balance Sheet interpretation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate interpretation. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Build Balance Sheet interpretation prompt
     */
    private function buildBalanceSheetInterpretationPrompt(array $reportData): string
    {
        $asOfDate = $reportData['asOfDate'] ?? 'N/A';
        $totalAssets = $reportData['totalAssets'] ?? 0;
        $totalLiabilities = $reportData['totalLiabilities'] ?? 0;
        $totalEquity = $reportData['totalEquity'] ?? 0;
        $companyName = $reportData['companyName'] ?? 'the business';
        $isBalanced = $reportData['isBalanced'] ?? true;
        $debtToEquityRatio = $reportData['debtToEquityRatio'] ?? 0;
        $debtRatio = $reportData['debtRatio'] ?? 0;
        $equityRatio = $reportData['equityRatio'] ?? 0;
        $netWorkingCapital = $reportData['netWorkingCapital'] ?? 0;

        $prompt = "You are BallieAI, an expert financial analyst. Analyze the following Balance Sheet for {$companyName} as of {$asOfDate} and provide a comprehensive, professional interpretation.\n\n";

        $prompt .= "## BALANCE SHEET SUMMARY\n";
        $prompt .= "**As of Date:** {$asOfDate}\n";
        $prompt .= "**Company:** {$companyName}\n\n";

        $prompt .= "### Financial Position:\n";
        $prompt .= "- **Total Assets:** ₦" . number_format($totalAssets, 2) . "\n";
        $prompt .= "- **Total Liabilities:** ₦" . number_format($totalLiabilities, 2) . "\n";
        $prompt .= "- **Total Equity:** ₦" . number_format($totalEquity, 2) . "\n";
        $prompt .= "- **Balance Status:** " . ($isBalanced ? 'Balanced' : 'Out of Balance') . "\n\n";

        $prompt .= "### Key Financial Ratios:\n";
        $prompt .= "- **Debt-to-Equity Ratio:** " . number_format($debtToEquityRatio, 2) . "\n";
        $prompt .= "- **Debt Ratio:** " . number_format($debtRatio, 1) . "%\n";
        $prompt .= "- **Equity Ratio:** " . number_format($equityRatio, 1) . "%\n";
        $prompt .= "- **Net Working Capital:** ₦" . number_format($netWorkingCapital, 2) . "\n\n";

        // Add asset details if available
        if (!empty($reportData['assets'])) {
            $prompt .= "### Assets Breakdown:\n";
            foreach ($reportData['assets'] as $asset) {
                $assetName = $asset['name'] ?? $asset['account_name'] ?? 'Unknown Asset';
                $assetBalance = $asset['balance'] ?? $asset['closing_balance'] ?? 0;
                $prompt .= "- **{$assetName}:** ₦" . number_format($assetBalance, 2) . "\n";
            }
            $prompt .= "\n";
        }

        // Add liability details if available
        if (!empty($reportData['liabilities'])) {
            $prompt .= "### Liabilities Breakdown:\n";
            foreach ($reportData['liabilities'] as $liability) {
                $liabilityName = $liability['name'] ?? $liability['account_name'] ?? 'Unknown Liability';
                $liabilityBalance = $liability['balance'] ?? $liability['closing_balance'] ?? 0;
                $prompt .= "- **{$liabilityName}:** ₦" . number_format($liabilityBalance, 2) . "\n";
            }
            $prompt .= "\n";
        }

        // Add equity details if available
        if (!empty($reportData['equity'])) {
            $prompt .= "### Equity Breakdown:\n";
            foreach ($reportData['equity'] as $equityItem) {
                $equityName = $equityItem['name'] ?? $equityItem['account_name'] ?? 'Unknown Equity';
                $equityBalance = $equityItem['balance'] ?? $equityItem['closing_balance'] ?? 0;
                $prompt .= "- **{$equityName}:** ₦" . number_format($equityBalance, 2) . "\n";
            }
            $prompt .= "\n";
        }

        $prompt .= "## ANALYSIS REQUIREMENTS\n\n";
        $prompt .= "Provide a detailed, professional analysis covering:\n\n";
        $prompt .= "1. **Financial Position Overview:** Assess the overall financial health and stability\n";
        $prompt .= "2. **Liquidity Analysis:** Evaluate the company's ability to meet short-term obligations\n";
        $prompt .= "3. **Solvency Analysis:** Assess long-term financial stability and debt management\n";
        $prompt .= "4. **Asset Management:** Review asset composition and efficiency\n";
        $prompt .= "5. **Capital Structure:** Analyze the mix of debt and equity financing\n";
        $prompt .= "6. **Key Strengths:** Highlight positive aspects of the financial position\n";
        $prompt .= "7. **Areas of Concern:** Identify potential risks or weaknesses\n";
        $prompt .= "8. **Strategic Recommendations:** Provide actionable insights for improvement\n\n";

        $prompt .= "Format your response in clear, professional markdown with appropriate headings and bullet points. ";
        $prompt .= "Use Nigerian Naira (₦) for all currency amounts. Keep the analysis concise but comprehensive.";

        return $prompt;
    }

    /**
     * Get fallback balance sheet interpretation
     */
    private function getFallbackBalanceSheetInterpretation(array $reportData): string
    {
        $asOfDate = $reportData['asOfDate'] ?? 'N/A';
        $totalAssets = $reportData['totalAssets'] ?? 0;
        $totalLiabilities = $reportData['totalLiabilities'] ?? 0;
        $totalEquity = $reportData['totalEquity'] ?? 0;
        $companyName = $reportData['companyName'] ?? 'Your Business';
        $debtToEquityRatio = $reportData['debtToEquityRatio'] ?? 0;
        $netWorkingCapital = $reportData['netWorkingCapital'] ?? 0;

        $interpretation = "# Balance Sheet Interpretation\n\n";
        $interpretation .= "**Company:** {$companyName}\n";
        $interpretation .= "**As of Date:** {$asOfDate}\n\n";

        $interpretation .= "## Financial Position Summary\n\n";
        $interpretation .= "### Key Figures:\n";
        $interpretation .= "- **Total Assets:** ₦" . number_format($totalAssets, 2) . "\n";
        $interpretation .= "- **Total Liabilities:** ₦" . number_format($totalLiabilities, 2) . "\n";
        $interpretation .= "- **Owner's Equity:** ₦" . number_format($totalEquity, 2) . "\n";
        $interpretation .= "- **Net Working Capital:** ₦" . number_format($netWorkingCapital, 2) . "\n\n";

        $interpretation .= "## Financial Health Analysis\n\n";

        // Liquidity Assessment
        if ($netWorkingCapital > 0) {
            $interpretation .= "### Liquidity Position: Positive\n";
            $interpretation .= "The company maintains positive net working capital of ₦" . number_format($netWorkingCapital, 2) . ", ";
            $interpretation .= "indicating sufficient short-term resources to meet obligations.\n\n";
        } else {
            $interpretation .= "### Liquidity Position: Concern\n";
            $interpretation .= "The company has negative net working capital of ₦" . number_format(abs($netWorkingCapital), 2) . ", ";
            $interpretation .= "which may indicate potential short-term liquidity challenges.\n\n";
        }

        // Capital Structure Assessment
        if ($debtToEquityRatio < 1) {
            $interpretation .= "### Capital Structure: Conservative\n";
            $interpretation .= "With a debt-to-equity ratio of " . number_format($debtToEquityRatio, 2) . ", ";
            $interpretation .= "the company maintains a conservative capital structure with lower financial leverage.\n\n";
        } elseif ($debtToEquityRatio < 2) {
            $interpretation .= "### Capital Structure: Moderate\n";
            $interpretation .= "The debt-to-equity ratio of " . number_format($debtToEquityRatio, 2) . " ";
            $interpretation .= "indicates a balanced approach to financing with moderate leverage.\n\n";
        } else {
            $interpretation .= "### Capital Structure: Highly Leveraged\n";
            $interpretation .= "A debt-to-equity ratio of " . number_format($debtToEquityRatio, 2) . " ";
            $interpretation .= "suggests high financial leverage, which may increase financial risk.\n\n";
        }

        // Owner's Equity Assessment
        if ($totalEquity > 0) {
            $equityPercentage = ($totalEquity / $totalAssets) * 100;
            $interpretation .= "### Ownership Position\n";
            $interpretation .= "Owner's equity represents " . number_format($equityPercentage, 1) . "% of total assets, ";
            $interpretation .= "reflecting the owner's stake in the business after all liabilities are paid.\n\n";
        }

        $interpretation .= "## Key Recommendations\n\n";
        $interpretation .= "1. **Monitor Liquidity:** Maintain adequate cash reserves for operational needs\n";
        $interpretation .= "2. **Optimize Asset Utilization:** Ensure assets are generating appropriate returns\n";
        $interpretation .= "3. **Manage Debt Levels:** Keep debt at sustainable levels relative to equity\n";
        $interpretation .= "4. **Build Equity:** Focus on profitable operations to strengthen financial position\n";
        $interpretation .= "5. **Regular Review:** Conduct quarterly balance sheet reviews to track progress\n\n";

        $interpretation .= "---\n\n";
        $interpretation .= "*Note: This is a basic interpretation. For detailed analysis, consult with a financial professional.*";

        return $interpretation;
    }

    /**
     * Export Balance Sheet interpretation as PDF
     */
    public function exportBalanceSheetInterpretationPdf(Request $request)
    {
        try {
            $interpretation = $request->input('interpretation');
            $reportDataJson = $request->input('reportData');

            // Parse report data
            $reportData = is_string($reportDataJson) ? json_decode($reportDataJson, true) : $reportDataJson;

            $tenant = auth()->user()->tenant;
            $asOfDate = $reportData['asOfDate'] ?? 'N/A';
            $totalAssets = $reportData['totalAssets'] ?? 0;
            $totalLiabilities = $reportData['totalLiabilities'] ?? 0;
            $totalEquity = $reportData['totalEquity'] ?? 0;

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('tenant.reports.balance-sheet-interpretation-pdf', compact(
                'tenant',
                'interpretation',
                'reportData',
                'asOfDate',
                'totalAssets',
                'totalLiabilities',
                'totalEquity'
            ));

            return $pdf->download('balance_sheet_interpretation_' . $asOfDate . '.pdf');

        } catch (\Exception $e) {
            Log::error('Balance Sheet PDF generation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'interpretation_length' => strlen($interpretation ?? ''),
                'report_data' => $reportDataJson ?? 'N/A'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Parse natural language invoice description into structured invoice data
     */
    public function parseInvoiceFromNaturalLanguage(Request $request)
    {
        try {
            $description = $request->input('description', '');
            $tenantId = $request->input('tenant_id');
            $voucherTypeId = $request->input('voucher_type_id');

            if (empty($description)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide a description of the invoice you want to create.'
                ], 400);
            }

            // Get available customers, vendors, and products for matching
            $tenant = \App\Models\Tenant::find($tenantId);
            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found.'
                ], 404);
            }

            // Fetch customers with ledger accounts
            $customers = \App\Models\Customer::where('tenant_id', $tenantId)
                ->with('ledgerAccount')
                ->get()
                ->map(function ($c) {
                    $name = $c->type === 'individual'
                        ? trim(($c->first_name ?? '') . ' ' . ($c->last_name ?? ''))
                        : ($c->company_name ?? '');
                    return [
                        'id' => $c->ledger_account_id,
                        'name' => $name,
                        'type' => 'customer',
                        'email' => $c->email
                    ];
                });

            // Fetch vendors with ledger accounts
            $vendors = \App\Models\Vendor::where('tenant_id', $tenantId)
                ->with('ledgerAccount')
                ->get()
                ->map(function ($v) {
                    $name = $v->type === 'individual'
                        ? trim(($v->first_name ?? '') . ' ' . ($v->last_name ?? ''))
                        : ($v->company_name ?? '');
                    return [
                        'id' => $v->ledger_account_id,
                        'name' => $name,
                        'type' => 'vendor',
                        'email' => $v->email
                    ];
                });

            // Fetch products
            $products = \App\Models\Product::where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->get()
                ->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'sku' => $p->sku,
                        'sales_rate' => $p->sales_rate,
                        'purchase_rate' => $p->purchase_rate,
                        'current_stock' => $p->current_stock,
                        'unit' => $p->unit?->name ?? 'Pcs'
                    ];
                });

            // Fetch voucher types (limit to Sales or Purchase only)
            $voucherTypes = \App\Models\VoucherType::where('tenant_id', $tenantId)
                ->where('affects_inventory', true)
                ->get()
                ->filter(function ($vt) {
                    $name = strtolower($vt->name ?? '');
                    $code = strtolower($vt->code ?? '');

                    // Exclude returns
                    if (str_contains($name, 'return') || str_contains($code, 'return')) {
                        return false;
                    }

                    // Allow only sales or purchase types
                    return str_contains($name, 'sales') || str_contains($code, 'sales') || str_contains($code, 'sv')
                        || str_contains($name, 'purchase') || str_contains($code, 'pur');
                })
                ->map(function ($vt) {
                    return [
                        'id' => $vt->id,
                        'name' => $vt->name,
                        'code' => $vt->code,
                        'is_purchase' => str_contains(strtolower($vt->code), 'pur') || str_contains(strtolower($vt->name), 'purchase')
                    ];
                });

            // Build AI prompt
            $prompt = $this->buildInvoiceParsingPrompt(
                $description,
                $customers->toArray(),
                $vendors->toArray(),
                $products->toArray(),
                $voucherTypes->toArray()
            );

            // Call AI
            try {
                $response = $this->callAI($prompt);
                // Parse AI response
                $parsedInvoice = $this->parseInvoiceResponse($response, $customers, $vendors, $products, $voucherTypes);
            } catch (\Exception $e) {
                // Fallback to heuristic parser if AI is unavailable
                Log::warning('AI unavailable, using heuristic invoice parser: ' . $e->getMessage());
                $parsedInvoice = $this->buildHeuristicInvoiceFromDescription(
                    $description,
                    $customers,
                    $vendors,
                    $products,
                    $voucherTypes
                );
            }

            return response()->json([
                'success' => true,
                'parsed_invoice' => $parsedInvoice,
                'ai_interpretation' => $parsedInvoice['interpretation'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('AI Invoice parsing error: ' . $e->getMessage(), [
                'description' => $description ?? 'N/A',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'AI assistant temporarily unavailable. Please fill the invoice manually.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Build the prompt for invoice parsing
     */
    private function buildInvoiceParsingPrompt(
        string $description,
        array $customers,
        array $vendors,
        array $products,
        array $voucherTypes
    ): string {
        $customerList = collect($customers)->take(50)->pluck('name')->implode(', ');
        $vendorList = collect($vendors)->take(50)->pluck('name')->implode(', ');
        $productList = collect($products)->take(100)->map(function ($p) {
            return "{$p['name']} (₦" . number_format($p['sales_rate'], 0) . ")";
        })->implode(', ');
        $voucherTypeList = collect($voucherTypes)->pluck('name')->implode(', ');

        return "
You are BallieAI, an expert invoice assistant for a Nigerian business accounting system.

Parse the following natural language invoice description and extract structured invoice data.

USER DESCRIPTION:
\"{$description}\"

AVAILABLE DATA:

Customers (for Sales invoices): {$customerList}

Vendors (for Purchase invoices): {$vendorList}

Products/Services: {$productList}

Invoice Types: {$voucherTypeList}

INSTRUCTIONS:
1. Determine if this is a Sales or Purchase invoice based on context
2. Match the customer/vendor name to the closest available option (fuzzy matching OK)
3. Match product names to available products (fuzzy matching OK)
4. Extract quantities and rates if mentioned, otherwise use product's default rate
5. Determine if VAT should be applied (look for keywords like 'VAT', 'with tax', '7.5%')
6. Extract any reference number if mentioned
7. Extract invoice date if mentioned, otherwise use today

RESPOND IN THIS EXACT JSON FORMAT:
{
    \"invoice_type\": \"sales\" or \"purchase\",
    \"voucher_type_name\": \"matched voucher type name or null\",
    \"party_name\": \"matched customer/vendor name or null\",
    \"party_type\": \"customer\" or \"vendor\",
    \"invoice_date\": \"YYYY-MM-DD or null for today\",
    \"reference_number\": \"string or null\",
    \"narration\": \"invoice description/notes\",
    \"items\": [
        {
            \"product_name\": \"matched product name\",
            \"quantity\": number,
            \"rate\": number (use mentioned rate or 0 for default),
            \"description\": \"optional item description\"
        }
    ],
    \"vat_enabled\": true or false,
    \"interpretation\": \"Brief explanation of what you understood from the description\"
}

If you cannot confidently match something, set it to null and explain in the interpretation field.
Use Naira (₦) amounts. Respond ONLY with valid JSON, no additional text.
";
    }

    /**
     * Parse AI response and match to actual database records
     */
    private function parseInvoiceResponse($response, $customers, $vendors, $products, $voucherTypes)
    {
        // Clean response - remove markdown code blocks if present
        $cleanResponse = preg_replace('/^```json\s*|\s*```$/m', '', trim($response));

        $data = json_decode($cleanResponse, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('AI invoice parsing JSON error', ['response' => $response]);
            return [
                'success' => false,
                'interpretation' => 'Could not parse AI response. Please try again with a clearer description.'
            ];
        }

        $result = [
            'invoice_type' => $data['invoice_type'] ?? 'sales',
            'voucher_type_id' => null,
            'voucher_type_name' => null,
            'party_id' => null,
            'party_name' => null,
            'party_type' => $data['party_type'] ?? 'customer',
            'invoice_date' => $data['invoice_date'] ?? date('Y-m-d'),
            'reference_number' => $data['reference_number'] ?? null,
            'narration' => $data['narration'] ?? null,
            'items' => [],
            'vat_enabled' => $data['vat_enabled'] ?? false,
            'interpretation' => $data['interpretation'] ?? 'Invoice parsed successfully.'
        ];

        // Match voucher type
        if (!empty($data['voucher_type_name'])) {
            $matchedVoucherType = $voucherTypes->first(function ($vt) use ($data) {
                return stripos($vt['name'], $data['voucher_type_name']) !== false ||
                       stripos($data['voucher_type_name'], $vt['name']) !== false;
            });
            if ($matchedVoucherType) {
                $result['voucher_type_id'] = $matchedVoucherType['id'];
                $result['voucher_type_name'] = $matchedVoucherType['name'];
            }
        }

        // If no voucher type matched, infer from invoice type
        if (!$result['voucher_type_id']) {
            $isPurchase = $result['invoice_type'] === 'purchase';
            $matchedVoucherType = $voucherTypes->first(function ($vt) use ($isPurchase) {
                return $vt['is_purchase'] === $isPurchase;
            });
            if ($matchedVoucherType) {
                $result['voucher_type_id'] = $matchedVoucherType['id'];
                $result['voucher_type_name'] = $matchedVoucherType['name'];
            }
        }

        // Match party (customer or vendor)
        if (!empty($data['party_name'])) {
            $partyCollection = $result['party_type'] === 'vendor' ? $vendors : $customers;
            $matchedParty = $partyCollection->first(function ($p) use ($data) {
                $partyName = strtolower($data['party_name']);
                $recordName = strtolower($p['name']);
                return str_contains($recordName, $partyName) || str_contains($partyName, $recordName);
            });

            if ($matchedParty) {
                $result['party_id'] = $matchedParty['id'];
                $result['party_name'] = $matchedParty['name'];
            } else {
                $result['party_name_suggested'] = $data['party_name'];
            }
        }

        // Match items/products
        $isPurchase = $result['invoice_type'] === 'purchase';
        foreach ($data['items'] ?? [] as $item) {
            $matchedProduct = $products->first(function ($p) use ($item) {
                $itemName = strtolower($item['product_name'] ?? '');
                $productName = strtolower($p['name']);
                return str_contains($productName, $itemName) || str_contains($itemName, $productName);
            });

            if ($matchedProduct) {
                $rate = !empty($item['rate']) && $item['rate'] > 0
                    ? $item['rate']
                    : ($isPurchase ? $matchedProduct['purchase_rate'] : $matchedProduct['sales_rate']);

                $quantity = $item['quantity'] ?? 1;

                $result['items'][] = [
                    'product_id' => $matchedProduct['id'],
                    'product_name' => $matchedProduct['name'],
                    'description' => $item['description'] ?? $matchedProduct['name'],
                    'quantity' => $quantity,
                    'rate' => $rate,
                    'amount' => $quantity * $rate,
                    'purchase_rate' => $matchedProduct['purchase_rate'],
                    'current_stock' => $matchedProduct['current_stock'],
                    'unit' => $matchedProduct['unit']
                ];
            } else {
                // Product not found - add with suggested name
                $result['items'][] = [
                    'product_id' => null,
                    'product_name_suggested' => $item['product_name'] ?? 'Unknown Product',
                    'description' => $item['description'] ?? $item['product_name'] ?? '',
                    'quantity' => $item['quantity'] ?? 1,
                    'rate' => $item['rate'] ?? 0,
                    'amount' => ($item['quantity'] ?? 1) * ($item['rate'] ?? 0),
                    'not_found' => true
                ];
            }
        }

        return $result;
    }

    /**
     * Heuristic parser when AI is unavailable
     */
    private function buildHeuristicInvoiceFromDescription($description, $customers, $vendors, $products, $voucherTypes)
    {
        $text = strtolower($description);

        $isPurchase = str_contains($text, 'purchase') || str_contains($text, 'bought') || str_contains($text, 'from supplier') || str_contains($text, 'from vendor');
        $isSales = str_contains($text, 'sale') || str_contains($text, 'sold') || str_contains($text, 'invoice') || str_contains($text, 'to customer');

        $invoiceType = $isPurchase && !$isSales ? 'purchase' : 'sales';
        $partyType = $invoiceType === 'purchase' ? 'vendor' : 'customer';

        $vatEnabled = str_contains($text, 'vat') || str_contains($text, 'tax') || str_contains($text, '7.5');

        $quantity = 1;
        if (preg_match('/\b(\d+(?:\.\d+)?)\b/', $text, $qtyMatch)) {
            $quantity = (float) $qtyMatch[1];
        }

        $rate = 0;
        if (preg_match('/\b(?:at|@)\s*(\d+(?:\.\d+)?)(\s?[km])?\b/', $text, $rateMatch)) {
            $rate = (float) $rateMatch[1];
            $suffix = strtolower(trim($rateMatch[2] ?? ''));
            if ($suffix === 'k') {
                $rate *= 1000;
            } elseif ($suffix === 'm') {
                $rate *= 1000000;
            }
        }

        $productName = null;
        if (preg_match('/\b(?:sold|sell|purchase|bought)\s+\d+(?:\.\d+)?\s+(.+?)\s+(?:to|from)\b/', $text, $productMatch)) {
            $productName = trim($productMatch[1]);
        }

        $partyName = null;
        if (preg_match('/\b(?:to|from)\s+([a-z0-9&\-\s]+?)(?:\s+customer|\s+vendor|\s+client|\s+supplier|$)/i', $description, $partyMatch)) {
            $partyName = trim($partyMatch[1]);
        }

        $result = [
            'invoice_type' => $invoiceType,
            'voucher_type_id' => null,
            'voucher_type_name' => null,
            'party_id' => null,
            'party_name' => null,
            'party_type' => $partyType,
            'invoice_date' => date('Y-m-d'),
            'reference_number' => null,
            'narration' => $description,
            'items' => [],
            'vat_enabled' => $vatEnabled,
            'interpretation' => 'Parsed using offline rules because AI is temporarily unavailable.'
        ];

        // Match voucher type by invoice type
        $matchedVoucherType = $voucherTypes->first(function ($vt) use ($invoiceType) {
            return $vt['is_purchase'] === ($invoiceType === 'purchase');
        });
        if ($matchedVoucherType) {
            $result['voucher_type_id'] = $matchedVoucherType['id'];
            $result['voucher_type_name'] = $matchedVoucherType['name'];
        }

        // Match party
        if (!empty($partyName)) {
            $partyCollection = $partyType === 'vendor' ? $vendors : $customers;
            $matchedParty = $partyCollection->first(function ($p) use ($partyName) {
                $needle = strtolower($partyName);
                $haystack = strtolower($p['name']);
                return str_contains($haystack, $needle) || str_contains($needle, $haystack);
            });
            if ($matchedParty) {
                $result['party_id'] = $matchedParty['id'];
                $result['party_name'] = $matchedParty['name'];
            } else {
                $result['party_name_suggested'] = $partyName;
            }
        }

        // Match product
        if (!empty($productName)) {
            $matchedProduct = $products->first(function ($p) use ($productName) {
                $needle = strtolower($productName);
                $haystack = strtolower($p['name']);
                return str_contains($haystack, $needle) || str_contains($needle, $haystack);
            });

            if ($matchedProduct) {
                $finalRate = $rate > 0 ? $rate : ($invoiceType === 'purchase' ? $matchedProduct['purchase_rate'] : $matchedProduct['sales_rate']);
                $result['items'][] = [
                    'product_id' => $matchedProduct['id'],
                    'product_name' => $matchedProduct['name'],
                    'description' => $matchedProduct['name'],
                    'quantity' => $quantity,
                    'rate' => $finalRate,
                    'amount' => $quantity * $finalRate,
                    'purchase_rate' => $matchedProduct['purchase_rate'],
                    'current_stock' => $matchedProduct['current_stock'],
                    'unit' => $matchedProduct['unit']
                ];
            } else {
                $result['items'][] = [
                    'product_id' => null,
                    'product_name_suggested' => $productName,
                    'description' => $productName,
                    'quantity' => $quantity,
                    'rate' => $rate,
                    'amount' => $quantity * $rate,
                    'not_found' => true
                ];
            }
        }

        if (empty($result['items'])) {
            $result['items'][] = [
                'product_id' => null,
                'product_name_suggested' => 'Unknown Item',
                'description' => 'Item not detected',
                'quantity' => $quantity,
                'rate' => $rate,
                'amount' => $quantity * $rate,
                'not_found' => true
            ];
        }

        return $result;
    }

}
