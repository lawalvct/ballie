@extends('payroll.portal.layout')

@section('title', 'Attendance')
@section('page-title', $employee->first_name . '\'s Attendance')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Attendance</h1>
        <p class="text-gray-600">Scan QR code to mark your attendance</p>
    </div>

    <!-- Today's Attendance Status -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Today's Status</h2>

        @if($todayAttendance)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="text-sm text-blue-600 font-medium mb-1">Clock In</div>
                    <div class="text-2xl font-bold text-blue-900">
                        {{ $todayAttendance->clock_in ? $todayAttendance->clock_in->format('h:i A') : '-' }}
                    </div>
                    @if($todayAttendance->late_minutes > 0)
                        <div class="text-xs text-red-600 mt-1">Late by {{ $todayAttendance->late_minutes }} min</div>
                    @endif
                </div>

                <div class="bg-green-50 rounded-lg p-4">
                    <div class="text-sm text-green-600 font-medium mb-1">Clock Out</div>
                    <div class="text-2xl font-bold text-green-900">
                        {{ $todayAttendance->clock_out ? $todayAttendance->clock_out->format('h:i A') : '-' }}
                    </div>
                    @if($todayAttendance->clock_out && $todayAttendance->work_hours_minutes > 0)
                        <div class="text-xs text-green-600 mt-1">{{ number_format($todayAttendance->work_hours_minutes / 60, 2) }} hrs</div>
                    @endif
                </div>

                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="text-sm text-purple-600 font-medium mb-1">Status</div>
                    <div class="text-2xl font-bold text-purple-900 capitalize">
                        {{ $todayAttendance->status }}
                    </div>
                    @if($todayAttendance->overtime_minutes > 0)
                        <div class="text-xs text-purple-600 mt-1">OT: {{ number_format($todayAttendance->overtime_minutes / 60, 2) }} hrs</div>
                    @endif
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                <p class="text-yellow-800">No attendance record for today. Scan QR code to clock in.</p>
            </div>
        @endif
    </div>

    <!-- QR Scanner Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h2M4 12h2m12 0h2m-6 0h-2m-2-8h2m-2 4h2m-2 4h2"/>
            </svg>
            Scan QR Code
        </h2>

        <div class="space-y-4">
            <!-- Scanner Container -->
            <div id="scanner-container" class="relative bg-gray-100 rounded-lg overflow-hidden" style="display: none;">
                <div id="qr-reader" style="width: 100%;"></div>
                <button id="stop-scan-btn" class="absolute top-4 right-4 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium shadow-lg">
                    Stop Scanner
                </button>
            </div>

            <!-- Start Scanner Button -->
            <button id="start-scan-btn" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-4 rounded-lg font-medium flex items-center justify-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Start QR Scanner
            </button>

            <!-- Status Messages -->
            <div id="scan-status" class="hidden"></div>
        </div>
    </div>

    <!-- Recent Attendance (Last 7 Days) -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Attendance</h2>

        @if($recentAttendance->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clock In</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clock Out</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hours</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentAttendance as $record)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $record->attendance_date->format('D, M d, Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $record->clock_in ? $record->clock_in->format('h:i A') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $record->clock_out ? $record->clock_out->format('h:i A') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $record->work_hours_minutes > 0 ? number_format($record->work_hours_minutes / 60, 2) . ' hrs' : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $record->status === 'present' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $record->status === 'late' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $record->status === 'absent' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $record->status === 'half_day' ? 'bg-orange-100 text-orange-800' : '' }}
                                        {{ $record->status === 'on_leave' ? 'bg-blue-100 text-blue-800' : '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <p>No recent attendance records</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<!-- Include html5-qrcode library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
let html5QrcodeScanner = null;

document.getElementById('start-scan-btn').addEventListener('click', function() {
    startScanner();
});

document.getElementById('stop-scan-btn').addEventListener('click', function() {
    stopScanner();
});

function startScanner() {
    // Check HTTPS requirement
    const isSecure = window.location.protocol === 'https:' || window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
    
    if (!isSecure) {
        showMessage('🔒 Camera requires HTTPS. Please access this page via HTTPS or contact your administrator.', 'error');
        showHttpsInstructions();
        return;
    }

    // Check if browser supports camera access
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        showMessage('❌ Your browser does not support camera access. Please use Chrome, Safari, or Firefox.', 'error');
        return;
    }

    // Show permission prompt message
    showMessage('Please allow camera access when prompted by your browser.', 'info');

    // Request camera permission explicitly with better error handling
    navigator.mediaDevices.getUserMedia({ 
        video: { 
            facingMode: "environment",
            width: { ideal: 1280 },
            height: { ideal: 720 }
        } 
    })
    .then(stream => {
        // Permission granted, stop the stream and start QR scanner
        stream.getTracks().forEach(track => track.stop());
        
        showMessage('Camera access granted. Starting scanner...', 'success');
        
        setTimeout(() => {
            document.getElementById('scanner-container').style.display = 'block';
            document.getElementById('start-scan-btn').style.display = 'none';

            html5QrcodeScanner = new Html5Qrcode("qr-reader");

            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0,
                rememberLastUsedCamera: true
            };

            html5QrcodeScanner.start(
                { facingMode: "environment" },
                config,
                onScanSuccess,
                onScanFailure
            ).catch(err => {
                console.error('Unable to start scanner:', err);
                showMessage('Error: Unable to start camera. Please try again.', 'error');
                stopScanner();
            });
        }, 1000);
    })
    .catch(err => {
        console.error('Camera permission error:', err);

        // Show detailed error message based on error type
        let errorMessage = '';
        let showInstructions = true;

        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
            errorMessage = '📷 Camera access was denied. Please allow camera access to scan QR codes.';
        } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
            errorMessage = '📷 No camera found on this device.';
            showInstructions = false;
        } else if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
            errorMessage = '📷 Camera is already in use by another application. Please close other apps using the camera.';
            showInstructions = false;
        } else if (err.name === 'OverconstrainedError') {
            errorMessage = '📷 Camera does not support the required settings. Trying with basic settings...';
            // Try again with basic settings
            retryWithBasicSettings();
            return;
        } else if (err.name === 'NotSupportedError') {
            errorMessage = '📷 Camera access requires a secure connection (HTTPS). Please use HTTPS or localhost.';
            showInstructions = false;
        } else {
            errorMessage = '📷 Unable to access camera. Please check your device settings and try again.';
        }

        showMessage(errorMessage, 'error');

        if (showInstructions) {
            showCameraInstructions();
        }
    });
}

function retryWithBasicSettings() {
    navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => {
        stream.getTracks().forEach(track => track.stop());
        showMessage('Camera access granted with basic settings. Starting scanner...', 'success');
        
        setTimeout(() => {
            document.getElementById('scanner-container').style.display = 'block';
            document.getElementById('start-scan-btn').style.display = 'none';

            html5QrcodeScanner = new Html5Qrcode("qr-reader");

            html5QrcodeScanner.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: 250 },
                onScanSuccess,
                onScanFailure
            ).catch(() => {
                showMessage('Error: Unable to start camera with basic settings.', 'error');
                stopScanner();
            });
        }, 1000);
    })
    .catch(() => {
        showMessage('📷 Camera access denied. Please allow camera access in your browser settings.', 'error');
        showCameraInstructions();
    });
}

function stopScanner() {
    if (html5QrcodeScanner) {
        html5QrcodeScanner.stop().then(() => {
            document.getElementById('scanner-container').style.display = 'none';
            document.getElementById('start-scan-btn').style.display = 'block';
            html5QrcodeScanner = null;
        }).catch(err => {
            console.error('Error stopping scanner:', err);
        });
    }
}

function onScanSuccess(decodedText, decodedResult) {
    // Stop scanner immediately to prevent multiple scans
    stopScanner();

    showMessage('Processing scan...', 'info');

    // Send scanned data to server
    fetch('{{ route("payroll.portal.scan-attendance", ["tenant" => $tenant, "token" => $token]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            qr_data: decodedText
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let message = data.message;
            if (data.clock_in_time) {
                message += ` at ${data.clock_in_time}`;
                if (data.late_minutes > 0) {
                    message += ` (Late by ${data.late_minutes} minutes)`;
                }
            }
            if (data.clock_out_time) {
                message += ` at ${data.clock_out_time}`;
                if (data.work_hours > 0) {
                    message += ` (${data.work_hours} hours worked)`;
                }
                if (data.overtime_hours > 0) {
                    message += ` (${data.overtime_hours} hours overtime)`;
                }
            }
            showMessage(message, 'success');

            // Reload page after 2 seconds to show updated status
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showMessage(data.error || 'Failed to process attendance', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred. Please try again.', 'error');
    });
}

function onScanFailure(error) {
    // Ignore scan failures (continuous scanning will keep trying)
}

function showMessage(message, type) {
    const statusDiv = document.getElementById('scan-status');
    statusDiv.className = 'p-4 rounded-lg ' +
        (type === 'success' ? 'bg-green-100 text-green-800' :
         type === 'error' ? 'bg-red-100 text-red-800' :
         'bg-blue-100 text-blue-800');
    statusDiv.textContent = message;
    statusDiv.classList.remove('hidden');

    // Hide message after 5 seconds for non-success messages
    if (type !== 'success') {
        setTimeout(() => {
            statusDiv.classList.add('hidden');
        }, 5000);
    }
}

function showHttpsInstructions() {
    const statusDiv = document.getElementById('scan-status');
    let instructions = '<div class="mt-4 p-4 bg-red-50 rounded-lg border border-red-200">';
    instructions += '<div class="text-red-800 font-semibold mb-3">🔒 HTTPS Required for Camera Access</div>';
    instructions += '<div class="text-sm text-red-700 space-y-2">';
    instructions += '<p>Modern browsers require a secure connection (HTTPS) to access the camera.</p>';
    instructions += '<div class="font-medium mt-3">Solutions:</div>';
    instructions += '<ol class="list-decimal ml-4 space-y-1">';
    instructions += '<li>Ask your administrator to enable HTTPS on the server</li>';
    instructions += '<li>Access via: <code class="bg-red-100 px-2 py-1 rounded">https://' + window.location.host + window.location.pathname + '</code></li>';
    instructions += '</ol>';
    instructions += '</div></div>';
    statusDiv.innerHTML = statusDiv.innerHTML + instructions;
}

function showCameraInstructions() {
    const statusDiv = document.getElementById('scan-status');
    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);
    const isAndroid = /Android/i.test(navigator.userAgent);

    let instructions = '<div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">';
    instructions += '<div class="text-blue-800 font-semibold mb-3">📱 How to enable camera access:</div>';
    instructions += '<div class="text-sm text-blue-700 space-y-2">';

    if (isIOS) {
        instructions += '<div class="font-medium">For iPhone/iPad:</div>';
        instructions += '<ol class="list-decimal ml-4 space-y-1">';
        instructions += '<li>Look for a camera icon in the address bar and tap it</li>';
        instructions += '<li>Select "Allow" when prompted</li>';
        instructions += '<li>If no icon appears, go to Settings > Safari > Camera > Allow</li>';
        instructions += '<li>Refresh this page and try again</li>';
        instructions += '</ol>';
    } else if (isAndroid) {
        instructions += '<div class="font-medium">For Android:</div>';
        instructions += '<ol class="list-decimal ml-4 space-y-1">';
        instructions += '<li>Tap the lock or info icon (🔒 or ⓘ) in the address bar</li>';
        instructions += '<li>Tap "Site settings" or "Permissions"</li>';
        instructions += '<li>Find "Camera" and set it to "Allow"</li>';
        instructions += '<li>Refresh this page and try again</li>';
        instructions += '</ol>';
        instructions += '<div class="mt-3 text-xs text-blue-600">💡 Alternative: Clear browser data for this site and try again</div>';
    } else {
        instructions += '<div class="font-medium">For Desktop:</div>';
        instructions += '<ol class="list-decimal ml-4 space-y-1">';
        instructions += '<li>Click the camera icon (📷) in the address bar</li>';
        instructions += '<li>Select "Always allow camera access"</li>';
        instructions += '<li>Refresh this page and try again</li>';
        instructions += '</ol>';
    }

    instructions += '</div>';
    instructions += '<div class="mt-3 text-xs text-blue-600">🔄 After changing settings, please refresh this page</div>';
    instructions += '</div>';

    // Add retry button
    instructions += '<div class="mt-3">';
    instructions += '<button onclick="location.reload()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">';
    instructions += '🔄 Refresh Page';
    instructions += '</button>';
    instructions += '</div>';

    statusDiv.innerHTML = statusDiv.innerHTML + instructions;
}
</script>
@endpush
