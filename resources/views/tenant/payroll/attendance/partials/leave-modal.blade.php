<!-- Mark Leave Modal -->
<div id="leaveModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900 bg-opacity-50">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Modal panel -->
        <div class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">

            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-umbrella-beach mr-2 text-purple-600"></i>
                    Mark Employee on Leave
                </h3>
                <button onclick="closeLeaveModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="leaveForm" action="{{ route('tenant.payroll.attendance.mark-leave', $tenant) }}" onsubmit="submitLeave(event)">
                <div class="space-y-4">
                    <!-- Employee Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Employee <span class="text-red-500">*</span>
                        </label>
                        <select id="leave_employee_id" name="employee_id" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="leave_date" name="date" required
                               value="{{ $selectedDate->format('Y-m-d') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>

                    <!-- Leave Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Leave Type <span class="text-red-500">*</span>
                        </label>
                        <select id="leave_type" name="leave_type" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="">Select Leave Type</option>
                            <option value="sick_leave">Sick Leave</option>
                            <option value="annual_leave">Annual Leave</option>
                            <option value="unpaid_leave">Unpaid Leave</option>
                            <option value="maternity_leave">Maternity Leave</option>
                            <option value="paternity_leave">Paternity Leave</option>
                            <option value="compassionate_leave">Compassionate Leave</option>
                        </select>
                    </div>

                    <!-- Reason -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Reason / Notes
                        </label>
                        <textarea id="leave_reason" name="reason" rows="3"
                                  placeholder="Optional: Additional details about the leave"
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"></textarea>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <p class="text-sm text-purple-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Note:</strong> This will mark the employee as on leave for the selected date. No work hours will be recorded.
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeLeaveModal()"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors duration-200">
                        <i class="fas fa-check mr-2"></i>
                        Mark as Leave
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
