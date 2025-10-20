<!-- Products Import Modal -->
<div id="importProductsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Import Products
                </h3>
                <button type="button" onclick="closeImportProductsModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="mt-4">
                <!-- Instructions (Collapsible) -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg mb-6">
                    <div class="flex items-center justify-between p-4 cursor-pointer" onclick="toggleInstructions()">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="ml-3 text-sm font-medium text-blue-800">How to import products</h3>
                        </div>
                        <svg id="instructions-icon" class="w-5 h-5 text-blue-600 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <div id="instructions-content" class="px-4 pb-4 hidden">
                        <div class="ml-8 text-sm text-blue-700">
                            <ol class="list-decimal pl-5 space-y-1">
                                <li>Download the template file</li>
                                <li>Download the categories reference (optional, for valid category names)</li>
                                <li>Fill in your product information in the template</li>
                                <li>Save the file and upload it below</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Download Buttons -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <a href="{{ route('tenant.inventory.products.export.template', ['tenant' => $tenant->slug]) }}"
                       class="inline-flex items-center justify-center px-4 py-3 border border-green-300 rounded-lg text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Template
                    </a>

                    <a href="{{ route('tenant.inventory.products.export.categories-reference', ['tenant' => $tenant->slug]) }}"
                       class="inline-flex items-center justify-center px-4 py-3 border border-blue-300 rounded-lg text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Categories Reference
                    </a>
                </div>

                <!-- Import Form -->
                <form id="importProductsForm" action="{{ route('tenant.inventory.products.import', ['tenant' => $tenant->slug]) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-4">
                        <!-- File Upload -->
                        <div>
                            <label for="import_file" class="block text-sm font-medium text-gray-700 mb-2">
                                Select Excel File <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-green-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="import_file" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                                            <span>Upload a file</span>
                                            <input id="import_file" name="import_file" type="file" class="sr-only" accept=".xlsx,.xls,.csv" required onchange="displayFileName(this)">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        Excel files (XLSX, XLS, CSV) up to 10MB
                                    </p>
                                    <p id="selected-file-name" class="text-sm font-medium text-green-600 mt-2 hidden"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Important Notes (Collapsible) -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-center justify-between p-4 cursor-pointer" onclick="toggleImportantNotes()">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="ml-3 text-sm font-medium text-yellow-800">Important Notes</h3>
                                </div>
                                <svg id="important-notes-icon" class="w-5 h-5 text-yellow-600 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                            <div id="important-notes-content" class="px-4 pb-4 hidden">
                                <div class="ml-8 text-sm text-yellow-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Required fields are marked with * in the template</li>
                                        <li>SKU must be unique per product</li>
                                        <li>Category names must match existing categories exactly</li>
                                        <li>Unit names must match existing units exactly</li>
                                        <li>Type must be either "item" or "service"</li>
                                        <li>Opening stock will create stock movement entries</li>
                                        <li>Existing products with the same SKU will be skipped</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end pt-4 border-t mt-6 space-x-3">
                <button type="button" onclick="closeImportProductsModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
                <button type="button" onclick="submitImportProducts()"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 flex items-center">
                    <span id="import-submit-text">Import Products</span>
                    <svg id="import-submit-loading" class="hidden animate-spin ml-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openImportProductsModal() {
    document.getElementById('importProductsModal').classList.remove('hidden');
}

function closeImportProductsModal() {
    document.getElementById('importProductsModal').classList.add('hidden');
    document.getElementById('importProductsForm').reset();
    document.getElementById('selected-file-name').classList.add('hidden');
    document.getElementById('import-submit-text').textContent = 'Import Products';
    document.getElementById('import-submit-loading').classList.add('hidden');
}

function toggleInstructions() {
    const content = document.getElementById('instructions-content');
    const icon = document.getElementById('instructions-icon');

    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        content.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}

function toggleImportantNotes() {
    const content = document.getElementById('important-notes-content');
    const icon = document.getElementById('important-notes-icon');

    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        content.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}

function displayFileName(input) {
    const fileNameDisplay = document.getElementById('selected-file-name');
    if (input.files && input.files[0]) {
        fileNameDisplay.textContent = `Selected: ${input.files[0].name}`;
        fileNameDisplay.classList.remove('hidden');
    } else {
        fileNameDisplay.classList.add('hidden');
    }
}

function submitImportProducts() {
    const form = document.getElementById('importProductsForm');
    const fileInput = document.getElementById('import_file');
    const submitButton = event.target;
    const submitText = document.getElementById('import-submit-text');
    const submitLoading = document.getElementById('import-submit-loading');

    if (!fileInput.files || !fileInput.files[0]) {
        alert('Please select a file to import');
        return;
    }

    // Show loading state
    submitButton.disabled = true;
    submitText.textContent = 'Importing...';
    submitLoading.classList.remove('hidden');

    // Submit the form
    form.submit();
}
</script>
