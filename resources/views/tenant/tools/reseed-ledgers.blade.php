@extends('layouts.tenant')

@section('title', 'Reseed Ledger Accounts - ' . $tenant->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">🔧 Ledger Accounts Troubleshooting</h1>
        <p class="text-gray-600">Use this tool if ledger accounts are missing or incomplete after onboarding.</p>
    </div>

    <!-- Current Status -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Current Status</h2>
        <div id="statusContainer" class="space-y-3">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <span class="text-gray-600">Ledger Accounts:</span>
                <span id="ledgerCount" class="text-2xl font-bold text-blue-600">Loading...</span>
            </div>
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <span class="text-gray-600">Account Groups:</span>
                <span id="groupCount" class="text-2xl font-bold text-green-600">Loading...</span>
            </div>
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <span class="text-gray-600">Voucher Types:</span>
                <span id="voucherCount" class="text-2xl font-bold text-purple-600">Loading...</span>
            </div>
        </div>
        <button onclick="checkStatus()" class="mt-4 text-blue-600 hover:text-blue-700 text-sm font-medium">
            🔄 Refresh Status
        </button>
    </div>

    <!-- Reseed Action -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Reseed Ledger Accounts</h2>
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-4">
            <div class="flex">
                <svg class="w-5 h-5 text-yellow-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <p class="text-sm text-yellow-700">
                        <strong>Note:</strong> This will add any missing ledger accounts. Existing accounts will not be duplicated.
                    </p>
                </div>
            </div>
        </div>

        <div id="reseedResult" class="hidden mb-4"></div>

        <button onclick="reseedLedgers()" id="reseedBtn"
                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span id="reseedBtnText">Reseed Ledger Accounts</span>
        </button>
    </div>

    <!-- Instructions -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-3">💡 When to Use This Tool</h3>
        <ul class="space-y-2 text-blue-800 text-sm">
            <li class="flex items-start">
                <svg class="w-5 h-5 mr-2 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span>If your onboarding timed out before all ledger accounts were created</span>
            </li>
            <li class="flex items-start">
                <svg class="w-5 h-5 mr-2 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span>If you see fewer than 100 ledger accounts in the status above</span>
            </li>
            <li class="flex items-start">
                <svg class="w-5 h-5 mr-2 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span>If you're missing key accounts like "Cash in Hand", "Bank Account", or "Sales Revenue"</span>
            </li>
        </ul>
    </div>
</div>

<script>
const tenantSlug = '{{ $tenant->slug }}';

// Check status on page load
document.addEventListener('DOMContentLoaded', function() {
    checkStatus();
});

async function checkStatus() {
    try {
        const response = await fetch(`/${tenantSlug}/onboarding/check-status`);
        const result = await response.json();

        if (result.success) {
            document.getElementById('ledgerCount').textContent = result.data.ledger_accounts;
            document.getElementById('groupCount').textContent = result.data.account_groups;
            document.getElementById('voucherCount').textContent = result.data.voucher_types;

            // Highlight if ledger count is low
            const ledgerCountEl = document.getElementById('ledgerCount');
            if (result.data.ledger_accounts < 50) {
                ledgerCountEl.classList.add('text-red-600');
                ledgerCountEl.classList.remove('text-blue-600');
            } else {
                ledgerCountEl.classList.add('text-blue-600');
                ledgerCountEl.classList.remove('text-red-600');
            }
        }
    } catch (error) {
        console.error('Error checking status:', error);
        showResult('Error checking status. Please refresh the page.', 'error');
    }
}

async function reseedLedgers() {
    const btn = document.getElementById('reseedBtn');
    const btnText = document.getElementById('reseedBtnText');
    const resultDiv = document.getElementById('reseedResult');

    // Disable button
    btn.disabled = true;
    btnText.textContent = 'Reseeding... This may take a minute';
    resultDiv.classList.add('hidden');

    try {
        const response = await fetch(`/${tenantSlug}/onboarding/reseed-ledgers`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const result = await response.json();

        if (result.success) {
            showResult(
                `✅ ${result.message}<br>
                <strong>Added:</strong> ${result.data.added} new ledger accounts<br>
                <strong>Total:</strong> ${result.data.new_count} ledger accounts`,
                'success'
            );
            // Refresh status
            setTimeout(checkStatus, 1000);
        } else {
            showResult(`❌ ${result.message}`, 'error');
        }
    } catch (error) {
        console.error('Error reseeding:', error);
        showResult('❌ An error occurred. Please try again or contact support.', 'error');
    } finally {
        // Re-enable button
        btn.disabled = false;
        btnText.textContent = 'Reseed Ledger Accounts';
    }
}

function showResult(message, type) {
    const resultDiv = document.getElementById('reseedResult');
    const bgColor = type === 'success' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200';
    const textColor = type === 'success' ? 'text-green-800' : 'text-red-800';

    resultDiv.className = `${bgColor} border rounded-lg p-4 mb-4`;
    resultDiv.innerHTML = `<p class="${textColor}">${message}</p>`;
    resultDiv.classList.remove('hidden');
}
</script>
@endsection
