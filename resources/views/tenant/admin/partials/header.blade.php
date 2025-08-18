{{-- Admin Module Header Component --}}
<div class="bg-white shadow">
    <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
        <div class="py-6 md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                {{-- Title and Breadcrumb --}}
                <div class="flex items-center">
                    <a href="{{ route('tenant.admin.index', tenant('slug')) }}" class="flex items-center text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.350 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Admin
                    </a>
                    @if(isset($breadcrumb))
                        <span class="mx-2 text-gray-400">/</span>
                        <span class="text-gray-900 font-medium">{{ $breadcrumb }}</span>
                    @endif
                </div>

                @if(isset($title))
                    <h1 class="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        {{ $title }}
                    </h1>
                @endif

                @if(isset($subtitle))
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>

            {{-- Action Buttons --}}
            @if(isset($actions))
                <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
                    {{ $actions }}
                </div>
            @endif
        </div>
    </div>
</div>
