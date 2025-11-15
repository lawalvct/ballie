@extends('layouts.tenant')

@section('title', 'Attendance QR Codes')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="qrCodeManager()">
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Attendance QR Codes</h1>
                <p class="text-gray-600 mt-1">Scan QR codes to mark attendance</p>
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
                <h2 class="text-2xl font-bold text-green-600">Clock In</h2>
            </div>
            <div id="clockInQr" class="bg-gray-50 rounded-lg p-8 flex justify-center items-center min-h-[400px]">
                <div x-show="!clockInQr" class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mx-auto mb-4"></div>
                    <p class="text-gray-600">Loading...</p>
                </div>
                <div x-show="clockInQr" x-html="clockInQr"></div>
            </div>
            <div class="mt-4 flex gap-2">
                <button @click="downloadQr('clock_in')" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    Download
                </button>
                <button @click="fullscreen('clockInQr')" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Fullscreen
                </button>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-center mb-4">
                <h2 class="text-2xl font-bold text-red-600">Clock Out</h2>
            </div>
            <div id="clockOutQr" class="bg-gray-50 rounded-lg p-8 flex justify-center items-center min-h-[400px]">
                <div x-show="!clockOutQr" class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-600 mx-auto mb-4"></div>
                    <p class="text-gray-600">Loading...</p>
                </div>
                <div x-show="clockOutQr" x-html="clockOutQr"></div>
            </div>
            <div class="mt-4 flex gap-2">
                <button @click="downloadQr('clock_out')" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                    Download
                </button>
                <button @click="fullscreen('clockOutQr')" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Fullscreen
                </button>
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
        },

        downloadQr(type) {
            const qrSvg = type === 'clock_in' ? this.clockInQr : this.clockOutQr;
            const blob = new Blob([qrSvg], { type: 'image/svg+xml' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = type + '_qr_code.svg';
            a.click();
            URL.revokeObjectURL(url);
        },

        fullscreen(elementId) {
            const element = document.getElementById(elementId);
            if (element.requestFullscreen) {
                element.requestFullscreen();
            } else if (element.webkitRequestFullscreen) {
                element.webkitRequestFullscreen();
            } else if (element.msRequestFullscreen) {
                element.msRequestFullscreen();
            }
        }
    }
}
</script>
@endpush
@endsection
