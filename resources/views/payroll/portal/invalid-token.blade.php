<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invalid Portal Link</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-red-50 to-orange-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Error Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8 text-center">
            <!-- Icon -->
            <div class="mb-6">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                    <i class="fas fa-exclamation-triangle text-4xl text-red-600"></i>
                </div>
            </div>

            <!-- Title -->
            <h1 class="text-2xl font-bold text-gray-900 mb-3">
                Invalid or Expired Portal Link
            </h1>

            <!-- Message -->
            <p class="text-gray-600 mb-6">
                The portal link you're trying to access is either invalid or has expired.
                Portal links are valid for 90 days from the date they were generated.
            </p>

            <!-- Reasons List -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
                <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Common Reasons:
                </h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start">
                        <i class="fas fa-circle text-xs mr-2 mt-1.5 text-gray-400"></i>
                        <span>The link has expired (older than 90 days)</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-circle text-xs mr-2 mt-1.5 text-gray-400"></i>
                        <span>The link was reset by your administrator</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-circle text-xs mr-2 mt-1.5 text-gray-400"></i>
                        <span>The link was incorrectly copied or pasted</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-circle text-xs mr-2 mt-1.5 text-gray-400"></i>
                        <span>Your employee account has been deactivated</span>
                    </li>
                </ul>
            </div>

            <!-- Action -->
            <div class="space-y-3">
                <p class="text-sm text-gray-700 font-medium">
                    What to do next:
                </p>
                <div class="bg-blue-50 rounded-lg p-4 text-left">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-envelope mr-2"></i>
                        Please contact your HR department or administrator to request a new portal access link.
                    </p>
                </div>
            </div>

            <!-- Help Section -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-500">
                    Need immediate assistance? Contact your company's HR department.
                </p>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-600">
                <i class="fas fa-shield-alt mr-1"></i>
                For security reasons, portal links expire after 90 days
            </p>
        </div>
    </div>

    <!-- Optional: Add animation -->
    <style>
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .shake {
            animation: shake 0.5s ease-in-out;
        }
    </style>

    <script>
        // Add shake animation on load
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.bg-white').classList.add('shake');
            setTimeout(() => {
                document.querySelector('.bg-white').classList.remove('shake');
            }, 500);
        });
    </script>
</body>
</html>
