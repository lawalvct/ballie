@extends('layouts.tenant')

@section('title', 'E-commerce Settings')
@section('page-title', 'E-commerce Store Settings')
@section('page-description', 'Configure your online store settings and preferences')

@section('content')
<div class="space-y-6">
    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">There were errors with your submission:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div class="flex-1 min-w-0">
            <!-- Page title is in the section -->
        </div>
        <div class="mt-4 lg:mt-0 flex gap-3">
            <a href="{{ route('tenant.ecommerce.orders.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                View Orders
            </a>
        </div>
    </div>

    @if($settings->exists && $settings->is_store_enabled)
    <!-- Store Access Section -->
    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg shadow-md p-6 border border-blue-200" x-data="storeAccessManager()">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                    </svg>
                    Your Store is Live!
                </h2>
                <p class="text-gray-600 mt-1">Share your store with customers using the link below</p>
            </div>
        </div>

        <!-- Store URL -->
        <div class="bg-white rounded-lg p-4 mb-4 border border-gray-200">
            <label class="block text-sm font-medium text-gray-700 mb-2">Store URL</label>
            <div class="flex gap-2">
                <input type="text"
                       id="storeUrl"
                       value="{{ url('/' . $tenant->slug . '/store') }}"
                       readonly
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 font-mono text-sm">
                <button @click="copyUrl"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <svg x-show="!copied" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <svg x-show="copied" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                </button>
                <a href="{{ url('/' . $tenant->slug . '/store') }}"
                   target="_blank"
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    Visit Store
                </a>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <!-- QR Code Button -->
            <button @click="toggleQrCode"
                    class="flex items-center justify-center gap-2 px-4 py-3 bg-white border-2 border-purple-300 text-purple-700 rounded-lg hover:bg-purple-50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
                <span x-text="showQr ? 'Hide QR Code' : 'Show QR Code'"></span>
            </button>

            <!-- Share on Social Media -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-white border-2 border-blue-300 text-blue-700 rounded-lg hover:bg-blue-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                    </svg>
                    Share on Social
                </button>

                <div x-show="open"
                     @click.away="open = false"
                     x-transition
                     class="absolute z-10 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2">
                    <a :href="shareUrl('facebook')" target="_blank" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Facebook
                    </a>
                    <a :href="shareUrl('twitter')" target="_blank" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition">
                        <svg class="w-5 h-5 text-sky-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                        Twitter
                    </a>
                    <a :href="shareUrl('whatsapp')" target="_blank" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        WhatsApp
                    </a>
                    <button @click="shareUrl('email')" class="w-full flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition text-left">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Email
                    </button>
                </div>
            </div>

            <!-- Download Promotional Materials -->
            <button @click="downloadQr"
                    class="flex items-center justify-center gap-2 px-4 py-3 bg-white border-2 border-green-300 text-green-700 rounded-lg hover:bg-green-50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Download QR Code
            </button>
        </div>

        <!-- QR Code Display -->
        <div x-show="showQr"
             x-transition
             class="mt-4 bg-white rounded-lg p-6 border border-gray-200">
            <div class="flex flex-col items-center">
                <h3 class="text-lg font-semibold mb-4">Store QR Code</h3>
                <div id="qrCodeContainer" class="bg-gray-50 rounded-lg p-8 flex justify-center items-center min-h-[320px]">
                    <div x-show="!qrCode" class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto mb-4"></div>
                        <p class="text-gray-600">Generating QR Code...</p>
                    </div>
                    <div x-show="qrCode" x-html="qrCode"></div>
                </div>
                <p class="text-sm text-gray-600 mt-4 text-center">
                    Print this QR code and display it in your physical store or on promotional materials.<br>
                    Customers can scan it to visit your online store instantly.
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Settings Form -->
    <form method="POST" action="{{ route('tenant.ecommerce.settings.update', ['tenant' => $tenant->slug]) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- General Settings -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">General Settings</h3>
            </div>
            <div class="p-6 space-y-6">
                <!-- Store Enabled -->
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700">Enable Store</label>
                        <p class="text-xs text-gray-500 mt-1">Turn your online store on or off</p>
                    </div>
                    <div class="ml-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_store_enabled" value="1" class="sr-only peer" {{ old('is_store_enabled', $settings->is_store_enabled ?? false) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>

                <!-- Store Name -->
                <div>
                    <label for="store_name" class="block text-sm font-medium text-gray-700">Store Name <span class="text-red-500">*</span></label>
                    <input type="text" name="store_name" id="store_name" value="{{ old('store_name', $settings->store_name ?? $tenant->name) }}" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                    @error('store_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Store Description -->
                <div>
                    <label for="store_description" class="block text-sm font-medium text-gray-700">Store Description</label>
                    <textarea name="store_description" id="store_description" rows="3"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">{{ old('store_description', $settings->store_description) }}</textarea>
                    @error('store_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Store Logo -->
                <div>
                    <label for="store_logo" class="block text-sm font-medium text-gray-700">Store Logo</label>
                    @if($settings->store_logo)
                        <div class="mt-2 mb-3">
                            <img src="{{ Storage::disk('public')->url($settings->store_logo) }}" alt="Store Logo" class="h-20 w-auto border border-gray-200 rounded-lg">
                        </div>
                    @endif
                    <input type="file" name="store_logo" id="store_logo" accept="image/*"
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                    @error('store_logo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Store Banner -->
                <div>
                    <label for="store_banner" class="block text-sm font-medium text-gray-700">Store Banner</label>
                    @if($settings->store_banner)
                        <div class="mt-2 mb-3">
                            <img src="{{ Storage::disk('public')->url($settings->store_banner) }}" alt="Store Banner" class="h-32 w-auto border border-gray-200 rounded-lg">
                        </div>
                    @endif
                    <input type="file" name="store_banner" id="store_banner" accept="image/*"
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF up to 2MB (Recommended: 1920x400px)</p>
                    @error('store_banner')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Customer Registration Settings -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Customer Registration</h3>
            </div>
            <div class="p-6 space-y-4">
                <!-- Allow Guest Checkout -->
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700">Allow Guest Checkout</label>
                        <p class="text-xs text-gray-500 mt-1">Customers can checkout without creating an account</p>
                    </div>
                    <div class="ml-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="allow_guest_checkout" value="1" class="sr-only peer" {{ old('allow_guest_checkout', $settings->allow_guest_checkout ?? true) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>

                <!-- Allow Email Registration -->
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700">Allow Email Registration</label>
                        <p class="text-xs text-gray-500 mt-1">Customers can create accounts with email/password</p>
                    </div>
                    <div class="ml-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="allow_email_registration" value="1" class="sr-only peer" {{ old('allow_email_registration', $settings->allow_email_registration ?? true) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>

                <!-- Allow Google Login -->
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700">Allow Google Login</label>
                        <p class="text-xs text-gray-500 mt-1">Customers can sign in with their Google account</p>
                    </div>
                    <div class="ml-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="allow_google_login" value="1" class="sr-only peer" {{ old('allow_google_login', $settings->allow_google_login ?? false) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>

                <!-- Require Phone Number -->
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700">Require Phone Number</label>
                        <p class="text-xs text-gray-500 mt-1">Phone number is required during checkout</p>
                    </div>
                    <div class="ml-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="require_phone_number" value="1" class="sr-only peer" {{ old('require_phone_number', $settings->require_phone_number ?? true) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing & Tax Settings -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Pricing & Tax</h3>
            </div>
            <div class="p-6 space-y-6">
                <!-- Currency -->
                <div>
                    <label for="default_currency" class="block text-sm font-medium text-gray-700">Default Currency <span class="text-red-500">*</span></label>
                    <select name="default_currency" id="default_currency" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="NGN" {{ old('default_currency', $settings->default_currency ?? 'NGN') === 'NGN' ? 'selected' : '' }}>Nigerian Naira (₦)</option>
                        <option value="USD" {{ old('default_currency', $settings->default_currency) === 'USD' ? 'selected' : '' }}>US Dollar ($)</option>
                        <option value="GBP" {{ old('default_currency', $settings->default_currency) === 'GBP' ? 'selected' : '' }}>British Pound (£)</option>
                        <option value="EUR" {{ old('default_currency', $settings->default_currency) === 'EUR' ? 'selected' : '' }}>Euro (€)</option>
                    </select>
                    @error('default_currency')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tax Enabled -->
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700">Enable Tax</label>
                        <p class="text-xs text-gray-500 mt-1">Apply tax to product prices</p>
                    </div>
                    <div class="ml-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="tax_enabled" value="1" class="sr-only peer" {{ old('tax_enabled', $settings->tax_enabled ?? true) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>

                <!-- Tax Percentage -->
                <div>
                    <label for="tax_percentage" class="block text-sm font-medium text-gray-700">Tax Percentage (%)</label>
                    <input type="number" name="tax_percentage" id="tax_percentage" step="0.01" min="0" max="100" value="{{ old('tax_percentage', $settings->tax_percentage ?? 7.5) }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                    @error('tax_percentage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Shipping Settings -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Shipping</h3>
            </div>
            <div class="p-6 space-y-4">
                <!-- Shipping Enabled -->
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700">Enable Shipping</label>
                        <p class="text-xs text-gray-500 mt-1">Charge shipping fees for orders</p>
                    </div>
                    <div class="ml-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="shipping_enabled" value="1" class="sr-only peer" {{ old('shipping_enabled', $settings->shipping_enabled ?? true) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <a href="{{ route('tenant.ecommerce.shipping-methods.index', ['tenant' => $tenant->slug]) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Manage Shipping Methods
                    </a>
                </div>
            </div>
        </div>

        <!-- SEO Settings -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">SEO Settings</h3>
            </div>
            <div class="p-6 space-y-6">
                <!-- Meta Title -->
                <div>
                    <label for="meta_title" class="block text-sm font-medium text-gray-700">Meta Title</label>
                    <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $settings->meta_title) }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                    @error('meta_title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Meta Description -->
                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description</label>
                    <textarea name="meta_description" id="meta_description" rows="3"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">{{ old('meta_description', $settings->meta_description) }}</textarea>
                    @error('meta_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Social Media Links -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Social Media</h3>
            </div>
            <div class="p-6 space-y-6">
                <!-- Facebook -->
                <div>
                    <label for="social_facebook" class="block text-sm font-medium text-gray-700">Facebook URL</label>
                    <input type="url" name="social_facebook" id="social_facebook" value="{{ old('social_facebook', $settings->social_facebook) }}"
                           placeholder="https://facebook.com/yourpage"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                    @error('social_facebook')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Instagram -->
                <div>
                    <label for="social_instagram" class="block text-sm font-medium text-gray-700">Instagram URL</label>
                    <input type="url" name="social_instagram" id="social_instagram" value="{{ old('social_instagram', $settings->social_instagram) }}"
                           placeholder="https://instagram.com/yourpage"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                    @error('social_instagram')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Twitter -->
                <div>
                    <label for="social_twitter" class="block text-sm font-medium text-gray-700">Twitter URL</label>
                    <input type="url" name="social_twitter" id="social_twitter" value="{{ old('social_twitter', $settings->social_twitter) }}"
                           placeholder="https://twitter.com/yourpage"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                    @error('social_twitter')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Theme Settings -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Theme Colors</h3>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Primary Color -->
                    <div>
                        <label for="theme_primary_color" class="block text-sm font-medium text-gray-700">Primary Color</label>
                        <div class="mt-1 flex items-center space-x-3">
                            <input type="color" name="theme_primary_color" id="theme_primary_color" value="{{ old('theme_primary_color', $settings->theme_primary_color ?? '#3B82F6') }}"
                                   class="h-10 w-20 border border-gray-300 rounded-lg">
                            <input type="text" value="{{ old('theme_primary_color', $settings->theme_primary_color ?? '#3B82F6') }}"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm" readonly>
                        </div>
                        @error('theme_primary_color')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Secondary Color -->
                    <div>
                        <label for="theme_secondary_color" class="block text-sm font-medium text-gray-700">Secondary Color</label>
                        <div class="mt-1 flex items-center space-x-3">
                            <input type="color" name="theme_secondary_color" id="theme_secondary_color" value="{{ old('theme_secondary_color', $settings->theme_secondary_color ?? '#10B981') }}"
                                   class="h-10 w-20 border border-gray-300 rounded-lg">
                            <input type="text" value="{{ old('theme_secondary_color', $settings->theme_secondary_color ?? '#10B981') }}"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm" readonly>
                        </div>
                        @error('theme_secondary_color')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('tenant.dashboard', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Save Settings
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function storeAccessManager() {
    return {
        copied: false,
        showQr: false,
        qrCode: null,
        storeUrl: '{{ url('/' . $tenant->slug . '/store') }}',
        storeName: '{{ $settings->store_name ?? $tenant->name }}',

        copyUrl() {
            const input = document.getElementById('storeUrl');
            input.select();
            document.execCommand('copy');

            this.copied = true;
            setTimeout(() => {
                this.copied = false;
            }, 2000);
        },

        async toggleQrCode() {
            this.showQr = !this.showQr;

            if (this.showQr && !this.qrCode) {
                await this.loadQrCode();
            }
        },

        async loadQrCode() {
            try {
                const url = '{{ route("tenant.ecommerce.settings.generate-qr", $tenant) }}';
                const response = await fetch(url);
                const data = await response.json();

                if (data.success) {
                    this.qrCode = data.qr_code;
                }
            } catch (error) {
                console.error('Error loading QR code:', error);
                alert('Failed to generate QR code. Please try again.');
            }
        },

        downloadQr() {
            if (!this.qrCode) {
                this.loadQrCode().then(() => {
                    this.performDownload();
                });
            } else {
                this.performDownload();
            }
        },

        performDownload() {
            const blob = new Blob([this.qrCode], { type: 'image/svg+xml' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'store_qr_code.svg';
            a.click();
            URL.revokeObjectURL(url);
        },

        shareUrl(platform) {
            const text = `Check out ${this.storeName} - Shop online now!`;
            const url = encodeURIComponent(this.storeUrl);
            const encodedText = encodeURIComponent(text);

            const urls = {
                facebook: `https://www.facebook.com/sharer/sharer.php?u=${url}`,
                twitter: `https://twitter.com/intent/tweet?url=${url}&text=${encodedText}`,
                whatsapp: `https://wa.me/?text=${encodedText}%20${url}`,
                email: `mailto:?subject=${encodedText}&body=${encodedText}%20${url}`
            };

            if (platform === 'email') {
                window.location.href = urls.email;
            } else {
                return urls[platform];
            }
        }
    }
}
</script>
@endpush
@endsection
