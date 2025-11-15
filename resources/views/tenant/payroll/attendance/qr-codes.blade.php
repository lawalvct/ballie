@extends('layouts.tenant')

@section('title', 'Attendance QR Codes')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="qrCodeManager()" x-init="init()">
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Attendance QR Codes</h1>
                <p class="text-gray-600 mt-1">Generate QR codes for employees to scan and mark attendance</p>
            </div>
            <a href="{{ route('tenant.payroll.attendance.index', $tenant) }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium">
                Back to Attendance
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-center mb-4">
                <h2 class="text-2xl font-bold text-gray-900">Clock In</h2>
                <p class="text-sm text-gray-600 mt-1">Scan this QR code to clock in</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-8 flex justify-center items-center min-h-[400px]">
                <div x-show="!clockInQr" class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mx-auto mb-4"></div>
                    <p class="text-gray-600">Loading...</p>
                </div>
                <div x-show="clockInQr" x-html="clockInQr"></div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-center mb-4">
                <h2 class="text-2xl font-bold text-gray-900">Clock Out</h2>
                <p class="text-sm text-gray-600 mt-1">Scan this QR code to clock out</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-8 flex justify-center items-center min-h-[400px]">
                <div x-show="!clockOutQr" class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-600 mx-auto mb-4"></div>
                    <p class="text-gray-600">Loading...</p>
                </div>
                <div x-show="clockOutQr" x-html="clockOutQr"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function qrCodeManager() {
    return {
        clockInQr: null,
        clockOutQr: null,

        init() {
            this.loadQrCodes();
        },

        async loadQrCodes() {
            await this.loadQrCode('clock_in');
            await this.loadQrCode('clock_out');
        },

        async loadQrCode(type) {
            try {
                const url = '{{ route("tenant.payroll.attendance.generate-qr", $tenant) }}';
                const response = await fetch(url + '?type=' + type);
                const data = await response.json();

                if (data.success) {
                    if (type === 'clock_in') {
                        this.clockInQr = data.qr_code;
                    } else {
                        this.clockOutQr = data.qr_code;
                    }
                }
            } catch (error) {
                console.error('Error loading QR code:', error);
            }
        }
    }
}
</script>
@endpush
@endsection
