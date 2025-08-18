{{-- Reusable Form Component --}}
<div class="bg-white shadow sm:rounded-lg">
    <form method="POST" action="{{ $action }}" {{ isset($enctype) ? 'enctype=' . $enctype : '' }}>
        @csrf
        @if(isset($method) && $method !== 'POST')
            @method($method)
        @endif

        {{-- Form Header --}}
        @if(isset($title) || isset($subtitle))
            <div class="px-4 py-5 sm:p-6 border-b border-gray-200">
                @if(isset($title))
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ $title }}
                    </h3>
                @endif
                @if(isset($subtitle))
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
        @endif

        {{-- Form Content --}}
        <div class="px-4 py-5 sm:p-6">
            {{ $slot }}
        </div>

        {{-- Form Actions --}}
        @if(isset($actions))
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 rounded-b-lg">
                {{ $actions }}
            </div>
        @endif
    </form>
</div>

{{-- Form Field Component --}}
@pushOnce('scripts')
<script>
    // Form validation helpers
    function showFieldError(fieldName, message) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.classList.add('border-red-300', 'text-red-900', 'placeholder-red-300', 'focus:ring-red-500', 'focus:border-red-500');
            field.classList.remove('border-gray-300', 'focus:ring-purple-500', 'focus:border-purple-500');

            let errorDiv = field.parentNode.querySelector('.field-error');
            if (!errorDiv) {
                errorDiv = document.createElement('p');
                errorDiv.className = 'field-error mt-2 text-sm text-red-600';
                field.parentNode.appendChild(errorDiv);
            }
            errorDiv.textContent = message;
        }
    }

    function clearFieldError(fieldName) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.classList.remove('border-red-300', 'text-red-900', 'placeholder-red-300', 'focus:ring-red-500', 'focus:border-red-500');
            field.classList.add('border-gray-300', 'focus:ring-purple-500', 'focus:border-purple-500');

            const errorDiv = field.parentNode.querySelector('.field-error');
            if (errorDiv) {
                errorDiv.remove();
            }
        }
    }
</script>
@endPushOnce
