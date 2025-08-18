@extends('layouts.tenant-onboarding')

@section('title', 'Team Setup - Ballie Setup')

@section('content')
<!-- Progress Steps -->
<div class="mb-8">
    <div class="flex items-center justify-center">
        <div class="flex items-center space-x-4 md:space-x-8 overflow-x-auto pb-2">
            <!-- Step 1 - Completed -->
            <div class="flex items-center flex-shrink-0">
                <div class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <span class="ml-3 text-sm font-medium text-green-600 whitespace-nowrap">Company Info</span>
            </div>

            <!-- Connector -->
            <div class="w-8 md:w-16 h-1 bg-green-500 rounded hidden sm:block"></div>

            <!-- Step 2 - Completed -->
            <div class="flex items-center flex-shrink-0">
                <div class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <span class="ml-3 text-sm font-medium text-green-600 whitespace-nowrap">Preferences</span>
            </div>

          <!-- Connector -->
          <div class="w-8 md:w-16 h-1 bg-brand-blue rounded hidden sm:block"></div>

          <!-- Step 3 - Active -->
          <div class="flex items-center flex-shrink-0">
              <div class="w-10 h-10 bg-brand-blue text-white rounded-full flex items-center justify-center font-semibold shadow-lg">
                  3
              </div>
              <span class="ml-3 text-sm font-medium text-brand-blue whitespace-nowrap">Team Setup</span>
          </div>

          <!-- Connector -->
          <div class="w-8 md:w-16 h-1 bg-gray-200 rounded hidden sm:block"></div>

          <!-- Step 4 -->
          <div class="flex items-center flex-shrink-0">
              <div class="w-10 h-10 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center font-semibold">
                  4
              </div>
              <span class="ml-3 text-sm font-medium text-gray-500 whitespace-nowrap">Complete</span>
          </div>
      </div>
  </div>
</div>

<!-- Main Form Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
  <!-- Header -->
  <div class="bg-gradient-to-r from-brand-purple to-brand-blue text-white p-6 md:p-8">
      <div class="text-center">
          <div class="w-16 h-16 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
          </div>
          <h2 class="text-2xl md:text-3xl font-bold mb-2">Invite your team</h2>
          <p class="text-purple-100">Add team members and set their roles and permissions.</p>
      </div>
  </div>

  <!-- Form Content -->
  <div class="p-6 md:p-8">
      <form id="team-form" method="POST" action="{{ route('tenant.onboarding.save-step', ['tenant' => $currentTenant->slug, 'step' => 'team']) }}" class="space-y-8">
          @csrf
          <input type="hidden" name="has_team_members" id="has-team-members" value="0">

          <!-- Skip Option -->
          <div class="bg-blue-50 rounded-lg p-4 flex items-center justify-between">
              <div class="flex items-center">
                  <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span class="text-sm text-blue-800">You can skip this step and add team members later from your dashboard.</span>
              </div>
              <button type="button" onclick="skipTeamSetup()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                  Skip for now
              </button>
          </div>

          <!-- Team Members Section -->
          <div id="team-members-section">
              <div class="flex items-center justify-between mb-6">
                  <h3 class="text-lg font-semibold text-gray-900">Team Members</h3>
                  <button type="button" onclick="addTeamMember()" class="bg-brand-blue text-white px-4 py-2 rounded-lg hover:bg-brand-dark-purple transition-colors text-sm font-medium flex items-center">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                      </svg>
                      Add Member
                  </button>
              </div>

              <!-- Team Member Template (Hidden) -->
              <script type="text/template" id="team-member-template">
                  <div class="team-member-item bg-gray-50 rounded-lg p-6 mb-4 border border-gray-200">
                      <div class="flex items-center justify-between mb-4">
                          <h4 class="font-medium text-gray-900">Team Member</h4>
                          <button type="button" onclick="removeTeamMember(this)" class="text-red-600 hover:text-red-800 text-sm">
                              Remove
                          </button>
                      </div>

                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                          <div>
                              <label class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                              <input type="text" name="team_members[][name]" required disabled
                                     class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors"
                                     placeholder="Enter full name">
                              <div class="text-red-500 text-xs mt-1 error-message hidden">Name is required</div>
                          </div>

                          <div>
                              <label class="block text-sm font-medium text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                              <input type="email" name="team_members[][email]" required disabled
                                     class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors"
                                     placeholder="email@company.com">
                              <div class="text-red-500 text-xs mt-1 error-message hidden">Valid email is required</div>
                          </div>

                          <div>
                              <label class="block text-sm font-medium text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                              <select name="team_members[][role]" required disabled
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors">
                                  <option value="">Select role</option>
                                  <option value="admin">Administrator</option>
                                  <option value="manager">Manager</option>
                                  <option value="accountant">Accountant</option>
                                  <option value="sales">Sales Representative</option>
                                  <option value="employee">Employee</option>
                              </select>
                              <div class="text-red-500 text-xs mt-1 error-message hidden">Role is required</div>
                          </div>

                          <div>
                              <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                              <input type="text" name="team_members[][department]" disabled
                                     class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors"
                                     placeholder="e.g., Sales, Accounting, Operations">
                          </div>
                      </div>
                  </div>
              </script>

              <!-- Team Members Container -->
              <div id="team-members-container">
                  <!-- Team members will be added here dynamically -->
              </div>

              <!-- Add First Member Button (shown when no members) -->
              <div id="no-members-message" class="text-center py-12 border-2 border-dashed border-gray-300 rounded-lg">
                  <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                  <h3 class="text-lg font-medium text-gray-900 mb-2">No team members added yet</h3>
                  <p class="text-gray-600 mb-4">Start by adding your first team member</p>
                  <button type="button" onclick="addTeamMember()" class="bg-brand-blue text-white px-6 py-3 rounded-lg hover:bg-brand-dark-purple transition-colors font-medium">
                      Add First Member
                  </button>
              </div>
          </div>

          <!-- Role Permissions Info -->
          <div class="bg-gray-50 rounded-lg p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Role Permissions</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div class="bg-white p-4 rounded-lg border border-gray-200">
                      <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                          <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                          Administrator
                      </h4>
                      <ul class="text-sm text-gray-600 space-y-1">
                          <li>• Full system access</li>
                          <li>• Manage users & settings</li>
                          <li>• Financial reports</li>
                          <li>• All modules</li>
                      </ul>
                  </div>

                  <div class="bg-white p-4 rounded-lg border border-gray-200">
                      <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                          <span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                          Manager
                      </h4>
                      <ul class="text-sm text-gray-600 space-y-1">
                          <li>• Manage team members</li>
                          <li>• View reports</li>
                          <li>• Approve transactions</li>
                          <li>• Most modules</li>
                      </ul>
                  </div>

                  <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                        <span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                        Manager
                    </h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Manage team members</li>
                        <li>• View reports</li>
                        <li>• Approve transactions</li>
                        <li>• Most modules</li>
                    </ul>
                </div>

                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                        <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                        Accountant
                    </h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Financial management</li>
                        <li>• Create/edit transactions</li>
                        <li>• Generate reports</li>
                        <li>• Limited modules</li>
                    </ul>
                </div>

                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                        <span class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></span>
                        Sales Representative
                    </h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Manage customers</li>
                        <li>• Create invoices/quotes</li>
                        <li>• Sales reports</li>
                        <li>• Sales-related modules</li>
                    </ul>
                </div>

                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                        <span class="w-3 h-3 bg-purple-500 rounded-full mr-2"></span>
                        Employee
                    </h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Basic access</li>
                        <li>• Limited data entry</li>
                        <li>• View assigned items</li>
                        <li>• Restricted modules</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex flex-col sm:flex-row justify-between space-y-4 sm:space-y-0 sm:space-x-4 pt-4 border-t border-gray-200">
            <a href="{{ route('tenant.onboarding.show-step', ['tenant' => $currentTenant->slug, 'step' => 'preferences']) }}"
               class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors text-center">
                Back to Preferences
            </a>
            <button type="submit" class="px-6 py-3 bg-brand-blue text-white rounded-lg font-medium hover:bg-brand-dark-purple transition-colors">
                Continue to Final Step
            </button>
        </div>
    </form>
</div>
</div>

<!-- Tips Section -->
<div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
<div class="flex items-start">
    <div class="flex-shrink-0">
        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </div>
    <div class="ml-3">
        <h3 class="text-lg font-medium text-yellow-800">Tips for Team Setup</h3>
        <div class="mt-2 text-sm text-yellow-700">
            <ul class="space-y-1">
                <li>• Team members will receive an email invitation to join your Ballie account</li>
                <li>• Each team member will create their own password when accepting the invitation</li>
                <li>• You can change roles and permissions at any time from your dashboard</li>
                <li>• Start with essential team members only - you can add more later</li>
            </ul>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
  let teamMemberCount = 0;

  // Initialize form with existing data if available
  document.addEventListener('DOMContentLoaded', function() {
      @if(isset($teamMembers) && count($teamMembers) > 0)
          @foreach($teamMembers as $member)
              addTeamMember({
                  name: "{{ $member['name'] }}",
                  email: "{{ $member['email'] }}",
                  role: "{{ $member['role'] }}",
                  department: "{{ $member['department'] ?? '' }}"
              });
          @endforeach
      @endif

      // Add form validation
      document.getElementById('team-form').addEventListener('submit', function(e) {
          if (!validateTeamForm()) {
              e.preventDefault();
              return false;
          }
          return true;
      });
  });

  // Add a new team member
  function addTeamMember(data = null) {
      // Get template HTML
      const templateHTML = document.getElementById('team-member-template').innerHTML;

      // Create a temporary container
      const container = document.createElement('div');
      container.innerHTML = templateHTML;

      // Get the team member item
      const teamMemberItem = container.firstElementChild;

      // Set unique IDs for the fields
      const index = teamMemberCount++;
      teamMemberItem.id = `team-member-${index}`;

      // Update input names with proper array indices and enable fields
      const inputs = teamMemberItem.querySelectorAll('input, select');
      inputs.forEach(input => {
          const name = input.getAttribute('name');
          input.setAttribute('name', name.replace('[]', `[${index}]`));

          // Enable the field (remove disabled attribute)
          input.removeAttribute('disabled');

          // Set values if data is provided
          if (data) {
              const field = name.match(/\[(.*?)\]/)[1];
              if (data[field]) {
                  if (input.tagName === 'SELECT') {
                      const option = Array.from(input.options).find(opt => opt.value === data[field]);
                      if (option) option.selected = true;
                  } else {
                      input.value = data[field];
                  }
              }
          }
      });

      // Add to the container
      document.getElementById('team-members-container').appendChild(teamMemberItem);

      // Update the hidden field to indicate we have team members
      document.getElementById('has-team-members').value = '1';

      // Add validation listeners
      addValidationListeners(teamMemberItem);
  }

  // Remove a team member
  function removeTeamMember(button) {
      const teamMember = button.closest('.team-member-item');
      teamMember.remove();

      // Show the "no members" message if no members left
      const container = document.getElementById('team-members-container');
      if (container.children.length === 0) {
          document.getElementById('no-members-message').style.display = 'block';
          document.getElementById('has-team-members').value = '0';
      }
  }

  // Skip team setup
  function skipTeamSetup() {
      // Clear any existing team members
      document.getElementById('team-members-container').innerHTML = '';
      document.getElementById('has-team-members').value = '0';

      // Submit the form
      document.getElementById('team-form').submit();
  }

  // Add validation listeners to a team member form
  function addValidationListeners(memberElement) {
      const inputs = memberElement.querySelectorAll('input[required], select[required]');

      inputs.forEach(input => {
          input.addEventListener('blur', function() {
              validateInput(this);
          });

          input.addEventListener('change', function() {
              validateInput(this);
          });
      });
  }

  // Validate a single input
  function validateInput(input) {
      const errorMessage = input.nextElementSibling;

      // Reset error state
      input.classList.remove('border-red-500');
      errorMessage.classList.add('hidden');

      // Check if empty
      if (!input.value.trim()) {
          input.classList.add('border-red-500');
          errorMessage.classList.remove('hidden');
          return false;
      }

      // Email validation
      if (input.type === 'email') {
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailRegex.test(input.value)) {
              input.classList.add('border-red-500');
              errorMessage.classList.remove('hidden');
              errorMessage.textContent = 'Please enter a valid email address';
              return false;
          }
      }

      return true;
  }

  // Validate the entire form
  function validateTeamForm() {
      // If no team members, form is valid (skipping)
      if (document.getElementById('has-team-members').value === '0') {
          return true;
      }

      let isValid = true;
      const teamMembers = document.querySelectorAll('.team-member-item');

      // Validate each team member
      teamMembers.forEach(member => {
          const requiredInputs = member.querySelectorAll('input[required], select[required]');

          requiredInputs.forEach(input => {
              if (!validateInput(input)) {
                  isValid = false;
              }
          });
      });

      // Show error message if validation fails
      if (!isValid) {
          const errorMessage = document.createElement('div');
          errorMessage.className = 'bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 mb-6';
          errorMessage.innerHTML = '<p>Please fill in all required fields for team members or remove incomplete entries.</p>';

          // Remove any existing error messages
          const existingError = document.querySelector('.bg-red-50.border.border-red-200');
          if (existingError) {
              existingError.remove();
          }

          // Add the error message at the top of the form
          const form = document.getElementById('team-form');
          form.insertBefore(errorMessage, form.firstChild);

          // Scroll to the error message
          errorMessage.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }

      return isValid;
  }
</script>
@endpush