@extends('layouts.tenant')

@section('title', 'Payment Successful')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-50 to-blue-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Success Icon -->
        <div class="text-center">
            <div class="mx-auto h-20 w-20 bg-green-100 rounded-full flex items-center justify-center mb-6">
                <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-2">Payment Successful!</h1>
            <p class="text-gray-600 mb-8">Your subscription has been updated successfully</p>
        </div>

        <!-- Success Details Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 space-y-6">
            <!-- Transaction Details -->
            @if(isset($payment))
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Transaction Details</h3>

                <div class="space-y-3">
                    @if($payment->reference)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Reference:</span>
                        <span class="font-medium text-gray-900">{{ $payment->reference }}</span>
                    </div>
                    @endif

                    @if($payment->amount)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Amount Paid:</span>
                        <span class="font-bold text-green-600">₦{{ number_format($payment->amount / 100, 2) }}</span>
                    </div>
                    @endif

                    <div class="flex justify-between">
                        <span class="text-gray-600">Payment Date:</span>
                        <span class="font-medium text-gray-900">{{ now()->format('M j, Y g:i A') }}</span>
                    </div>

                    @if($payment->method)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Payment Method:</span>
                        <span class="font-medium text-gray-900">{{ ucfirst($payment->method) }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Subscription Details -->
            @if(isset($subscription) || isset($plan))
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Subscription Details</h3>

                @if(isset($plan))
                <div class="bg-blue-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $plan->name }}</h4>
                            <p class="text-gray-600 text-sm">{{ $plan->description }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold text-gray-900">{{ $plan->formatted_monthly_price }}</span>
                            <span class="text-gray-600 text-sm">/month</span>
                        </div>
                    </div>
                </div>
                @endif

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm font-medium">Active</span>
                    </div>

                    @if(isset($nextBillingDate))
                    <div class="flex justify-between">
                        <span class="text-gray-600">Next Billing:</span>
                        <span class="font-medium text-gray-900">{{ $nextBillingDate->format('M j, Y') }}</span>
                    </div>
                    @endif

                    @if(isset($billingCycle))
                    <div class="flex justify-between">
                        <span class="text-gray-600">Billing Cycle:</span>
                        <span class="font-medium text-gray-900">{{ ucfirst($billingCycle) }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- What's Next -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">What's Next?</h3>

                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-semibold text-sm">1</span>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Access Your Features</h4>
                            <p class="text-gray-600 text-sm">All premium features are now available in your account</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-semibold text-sm">2</span>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Check Your Email</h4>
                            <p class="text-gray-600 text-sm">We've sent a confirmation email with your receipt</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-semibold text-sm">3</span>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Manage Your Subscription</h4>
                            <p class="text-gray-600 text-sm">View billing history and manage your plan anytime</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-4">
            <a href="{{ route('tenant.dashboard', tenant()->slug) }}"
               class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2v0a2 2 0 002-2h10"/>
                </svg>
                Go to Dashboard
            </a>

            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('tenant.subscription.index', tenant()->slug) }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-4 rounded-lg transition-colors text-center">
                    View Subscription
                </a>

                @if(isset($payment) && $payment->invoice_url)
                <a href="{{ $payment->invoice_url }}" target="_blank"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-4 rounded-lg transition-colors text-center">
                    Download Receipt
                </a>
                @else
                <a href="{{ route('tenant.subscription.history', tenant()->slug) }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-4 rounded-lg transition-colors text-center">
                    View History
                </a>
                @endif
            </div>
        </div>

        <!-- Help Section -->
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <h4 class="font-medium text-gray-900 mb-2">Need Help?</h4>
            <p class="text-gray-600 text-sm mb-3">If you have any questions about your subscription or payment</p>
            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Contact Support</a>
        </div>

        <!-- Security Notice -->
        <div class="flex items-center justify-center text-center">
            <div class="flex items-center text-gray-500 text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span>Your payment was processed securely</span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-redirect to dashboard after 30 seconds if user doesn't take action
    setTimeout(function() {
        if (confirm('Would you like to go to your dashboard now?')) {
            window.location.href = "{{ route('tenant.dashboard', tenant()->slug) }}";
        }
    }, 30000);

    // Confetti animation on load
    function createConfetti() {
        const colors = ['#f43f5e', '#10b981', '#3b82f6', '#f59e0b', '#8b5cf6'];

        for (let i = 0; i < 50; i++) {
            setTimeout(() => {
                const confetti = document.createElement('div');
                confetti.style.position = 'fixed';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.top = '-10px';
                confetti.style.width = '10px';
                confetti.style.height = '10px';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.pointerEvents = 'none';
                confetti.style.borderRadius = '50%';
                confetti.style.zIndex = '9999';
                confetti.style.animation = 'fall 3s linear forwards';

                document.body.appendChild(confetti);

                setTimeout(() => {
                    if (confetti.parentNode) {
                        confetti.parentNode.removeChild(confetti);
                    }
                }, 3000);
            }, i * 100);
        }
    }

    // Add CSS for confetti animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fall {
            to {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);

    // Trigger confetti
    createConfetti();
});
</script>
@endsection
