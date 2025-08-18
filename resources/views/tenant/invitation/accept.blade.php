@extends('layouts.app')

@section('title', 'Accept Invitation - ' . $tenant->name)

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center">
            @if($tenant->logo)
                <img class="mx-auto h-16 w-auto" src="{{ Storage::url($tenant->logo) }}" alt="{{ $tenant->name }}">
            @else
                <div class="mx-auto h-16 w-16 bg-brand-blue rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-xl">{{ strtoupper(substr($tenant->name, 0, 2)) }}</span>
                </div>
            @endif
            <h2 class="mt-6 text-3xl font-bold text-gray-900">Join {{ $tenant->name }}</h2>
            <p class="mt-2 text-sm text-gray-600">
                You've been invited to join {{ $tenant->name }} on Ballie
            </p>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <div class="mb-6">
                <div class="flex items-center p-4 bg-blue-50 rounded-lg">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>{{ $user->name }}</strong> ({{ $user->email }})
                        </p>
                    </div>
                </div>
            </div>

            @if(!$user->email_verified_at)
                <form method="POST" action="{{ route('tenant.invitation.process', ['tenant' => $tenant->slug, 'token' => $token]) }}">
                    @csrf

                    <div class="mb-6">
                        <p class="text-sm text-gray-600 mb-4">
                            To complete your invitation, please set a secure password for your account.
                        </p>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="password" name="password" type="password" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-brand-blue focus:border-brand-blue">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-brand-blue focus:border-brand-blue">
                    </div>

                    <div>
                        <button type="submit"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-brand-blue hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-blue">
                            Accept Invitation & Join Team
                        </button>
                    </div>
                </form>
            @else
                <form method="POST" action="{{ route('tenant.invitation.process', ['tenant' => $tenant->slug, 'token' => $token]) }}">
                    @csrf

                    <div class="mb-6">
                        <p class="text-sm text-gray-600">
                            Click the button below to accept your invitation and join {{ $tenant->name }}.
                        </p>
                    </div>

                    <div>
                        <button type="submit"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-brand-blue hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-blue">
                            Accept Invitation & Join Team
                        </button>
                    </div>
                </form>
            @endif

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Company Information</span>
                    </div>
                </div>

                <div class="mt-4 bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">{{ $tenant->name }}</h3>
                    @if($tenant->business_type)
                        <p class="text-xs text-gray-600">{{ $tenant->business_type }}</p>
                    @endif
                    @if($tenant->industry)
                        <p class="text-xs text-gray-600">{{ $tenant->industry }}</p>
                    @endif
                    @if($tenant->website)
                        <p class="text-xs text-gray-600">
                            <a href="{{ $tenant->website }}" target="_blank" class="text-brand-blue hover:text-blue-800">
                                {{ $tenant->website }}
                            </a>
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500">
                By accepting this invitation, you agree to Ballie's
                <a href="#" class="text-brand-blue hover:text-blue-800">Terms of Service</a> and
                <a href="#" class="text-brand-blue hover:text-blue-800">Privacy Policy</a>
            </p>
        </div>
    </div>
</div>
@endsection