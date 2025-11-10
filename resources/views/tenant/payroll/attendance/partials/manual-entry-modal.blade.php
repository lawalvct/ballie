<!-- Manual Attendance Entry Modal -->
<div id="manualEntryModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900 bg-opacity-50">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Modal panel -->
        <div class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">

            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-clock mr-2 text-blue-600"></i>
                    Manual Attendance Entry
                </h3>
                <button onclick="closeManualEntryModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="manualEntryForm" action="{{ route('tenant.payroll.attendance.manual-entry', $tenant) }}" onsubmit="submitManualEntry(event)">
                <div class="space-y-4">
                    <!-- Employee Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Employee <span class="text-red-500">*</span>
                        </label>
                        <select id="manual_employee_id" name="employee_id" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }} - {{ $emp->department->name ?? 'N/A' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="manual_date" name="date" required
                               value="{{ $selectedDate->format('Y-m-d') }}"
                               max="{{ now()->format('Y-m-d') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Clock In & Clock Out Times -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Clock In Time <span class="text-red-500">*</span>
                            </label>
                            <input type="time" id="manual_clock_in_time" name="clock_in_time" required
                                   onchange="calculateWorkHoursPreview()"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Clock Out Time
                            </label>
                            <input type="time" id="manual_clock_out_time" name="clock_out_time"
                                   onchange="calculateWorkHoursPreview()"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Break Minutes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Break Duration (minutes)
                        </label>
                        <input type="number" id="manual_break_minutes" name="break_minutes" min="0" max="480" step="15" value="60"
                               onchange="calculateWorkHoursPreview()"
                               placeholder="e.g., 60 for 1 hour"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Standard lunch break is usually 60 minutes</p>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Notes
                        </label>
                        <textarea id="manual_notes" name="notes" rows="3"
                                  placeholder="Reason for manual entry, special circumstances, etc."
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>

                    <!-- Work Hours Preview -->
                    <div id="workHoursPreview" class="bg-blue-50 border border-blue-200 rounded-lg p-4" style="display: none;">
                        <p class="text-sm font-medium text-blue-900 mb-2">
                            <i class="fas fa-info-circle mr-1"></i> Work Hours Calculation:
                        </p>
                        <p id="workHoursText" class="text-sm text-blue-700"></p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeManualEntryModal()"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Save Attendance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
