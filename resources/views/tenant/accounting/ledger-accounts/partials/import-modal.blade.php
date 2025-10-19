<!-- Import Ledger Accounts Modal -->
<div id="importModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('importModal').classList.add('hidden')"></div>

        <!-- Center modal -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
            <form action="{{ route('tenant.accounting.ledger-accounts.import', ['tenant' => $tenant->slug]) }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <!-- Header -->
                    <div class="flex items-start mb-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                        </div>
                        <div class="mt-0 ml-4 text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Import Ledger Accounts
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Upload your ledger accounts data from an Excel or CSV file
                            </p>
                        </div>
                    </div>

                    <!-- Collapsible Instructions -->
                    <div class="mb-4">
                        <button type="button" onclick="document.getElementById('importInstructions').classList.toggle('hidden')"
                                class="flex items-center text-sm font-medium text-blue-600 hover:text-blue-700 focus:outline-none">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Click here for import instructions
                        </button>
                        <div id="importInstructions" class="hidden mt-2 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-blue-900 mb-2">Instructions:</h4>
                            <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                                <li>Download the template file to see required format</li>
                                <li><strong>Download Account Groups Reference</strong> to see all available groups and their account types</li>
                                <li>Account code must be unique</li>
                                <li>Account group name must match existing group (use reference file)</li>
                                <li>Supported account types: asset, liability, income, expense, equity</li>
                                <li>Balance type: dr (debit) or cr (credit)</li>
                                <li>Supported formats: .xlsx, .xls, .csv</li>
                                <li>Maximum file size: 10MB</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Download Template -->
                    <div class="mb-4">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('tenant.accounting.ledger-accounts.export.template', ['tenant' => $tenant->slug]) }}"
                               class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download Template File
                            </a>

                            <a href="{{ route('tenant.accounting.ledger-accounts.export.account-groups', ['tenant' => $tenant->slug]) }}"
                               class="inline-flex items-center justify-center px-4 py-2 border border-blue-300 shadow-sm text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download Account Groups Reference
                            </a>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            <strong>Account Groups Reference:</strong> Download this file to see all available account groups and their account types for use in your import.
                        </p>
                    </div>

                    <!-- File Upload -->
                    <div class="mb-4">
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                            Select File to Import
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload a file</span>
                                        <input id="file" name="file" type="file" class="sr-only" accept=".xlsx,.xls,.csv" required onchange="updateFileName(this)">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">
                                    Excel or CSV up to 10MB
                                </p>
                                <p id="file-name" class="text-sm font-medium text-gray-900 mt-2"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Column Descriptions -->
                    <div class="mb-4">
                        <button type="button" onclick="document.getElementById('columnGuide').classList.toggle('hidden')"
                                class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            View Column Descriptions
                        </button>
                        <div id="columnGuide" class="hidden mt-2 bg-gray-50 border border-gray-200 rounded-lg p-4 max-h-60 overflow-y-auto">
                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div><strong>code:</strong> Unique account code</div>
                                <div><strong>name:</strong> Account name</div>
                                <div><strong>account_type:</strong> asset/liability/income/expense/equity</div>
                                <div><strong>account_group:</strong> Name of account group</div>
                                <div><strong>parent_code:</strong> Parent account code (optional)</div>
                                <div><strong>balance_type:</strong> dr or cr</div>
                                <div><strong>opening_balance:</strong> Opening balance amount</div>
                                <div><strong>description:</strong> Account description</div>
                                <div><strong>address:</strong> Account address (optional)</div>
                                <div><strong>phone:</strong> Contact phone (optional)</div>
                                <div><strong>email:</strong> Contact email (optional)</div>
                                <div><strong>is_active:</strong> yes or no</div>
                                <div><strong>opening_balance_date:</strong> Date (YYYY-MM-DD)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" id="submitBtn"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        Import Accounts
                    </button>
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateFileName(input) {
    const fileName = input.files[0]?.name;
    const fileNameDisplay = document.getElementById('file-name');
    if (fileName) {
        fileNameDisplay.textContent = '✓ Selected: ' + fileName;
    } else {
        fileNameDisplay.textContent = '';
    }
}

// Close modal when clicking outside
document.getElementById('importModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});

// Handle form submission
document.getElementById('importForm')?.addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Importing...';
});
</script>
