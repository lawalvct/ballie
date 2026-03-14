{{-- Project Show Scripts --}}
@push('scripts')
<script>
    const PROJECT_ID = {{ $project->id }};
    const TENANT_SLUG = '{{ $tenant->slug }}';
    const BASE_URL = `/{{ $tenant->slug }}/projects/${PROJECT_ID}`;
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content;

    function projectShow() {
        return {
            activeTab: '{{ $tab }}',
            // Task
            newTask: { title: '', priority: 'medium', assigned_to: '', due_date: '' },
            taskLoading: false,
            // Milestone
            newMilestone: { title: '', amount: '', due_date: '', is_billable: true },
            milestoneAmountDisplay: '',
            milestoneLoading: false,
            // Note
            newNote: { content: '', is_internal: true },
            noteLoading: false,
            // File
            selectedFile: null,
            fileLoading: false,
            // Expense
            newExpense: { title: '', amount: '', expense_date: '', category: 'general', description: '' },
            expenseAmountDisplay: '',
            expenseLoading: false,

            setExpenseAmount(value) {
                const sanitized = value.replace(/,/g, '').replace(/[^\d.]/g, '');

                if (!sanitized) {
                    this.newExpense.amount = '';
                    this.expenseAmountDisplay = '';
                    return;
                }

                const hasDecimal = sanitized.includes('.');
                const [wholePartRaw, ...decimalParts] = sanitized.split('.');
                const wholePart = wholePartRaw.replace(/^0+(?=\d)/, '');
                const decimalPart = decimalParts.join('').slice(0, 2);
                const normalizedWhole = wholePart === '' ? '0' : wholePart;

                this.newExpense.amount = hasDecimal
                    ? `${normalizedWhole}.${decimalPart}`
                    : normalizedWhole;

                const formattedWhole = Number(normalizedWhole).toLocaleString('en-NG');
                this.expenseAmountDisplay = hasDecimal
                    ? `${formattedWhole}.${decimalPart}`
                    : formattedWhole;
            },

            setMilestoneAmount(value) {
                const sanitized = value.replace(/,/g, '').replace(/[^\d.]/g, '');

                if (!sanitized) {
                    this.newMilestone.amount = '';
                    this.milestoneAmountDisplay = '';
                    return;
                }

                const hasDecimal = sanitized.includes('.');
                const [wholePartRaw, ...decimalParts] = sanitized.split('.');
                const wholePart = wholePartRaw.replace(/^0+(?=\d)/, '');
                const decimalPart = decimalParts.join('').slice(0, 2);
                const normalizedWhole = wholePart === '' ? '0' : wholePart;

                this.newMilestone.amount = hasDecimal
                    ? `${normalizedWhole}.${decimalPart}`
                    : normalizedWhole;

                const formattedWhole = Number(normalizedWhole).toLocaleString('en-NG');
                this.milestoneAmountDisplay = hasDecimal
                    ? `${formattedWhole}.${decimalPart}`
                    : formattedWhole;
            },

            async addTask() {
                this.taskLoading = true;
                try {
                    const res = await fetch(`${BASE_URL}/tasks`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                        body: JSON.stringify(this.newTask)
                    });
                    if (res.ok) {
                        this.newTask = { title: '', priority: 'medium', assigned_to: '', due_date: '' };
                        window.location.reload();
                    } else {
                        const data = await res.json();
                        alert(data.message || 'Failed to add task');
                    }
                } catch (e) { alert('Error adding task'); }
                this.taskLoading = false;
            },

            async addMilestone() {
                this.milestoneLoading = true;
                try {
                    const res = await fetch(`${BASE_URL}/milestones`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                        body: JSON.stringify(this.newMilestone)
                    });
                    const data = await res.json();
                    if (res.ok) {
                        appendMilestoneRow(data.milestone);
                        updateCount('milestones-count-badge', 1);
                        this.newMilestone = { title: '', amount: '', due_date: '', is_billable: true };
                        this.milestoneAmountDisplay = '';
                    } else {
                        alert(data.message || 'Failed to add milestone');
                    }
                } catch (e) { alert('Error adding milestone'); }
                this.milestoneLoading = false;
            },

            async addNote() {
                this.noteLoading = true;
                try {
                    const res = await fetch(`${BASE_URL}/notes`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                        body: JSON.stringify(this.newNote)
                    });
                    const data = await res.json();
                    if (res.ok) {
                        appendNoteRow(data.note);
                        updateCount('notes-count-badge', 1);
                        this.newNote = { content: '', is_internal: true };
                    } else {
                        alert(data.message || 'Failed to add note');
                    }
                } catch (e) { alert('Error adding note'); }
                this.noteLoading = false;
            },

            async uploadFile() {
                if (!this.selectedFile) return;
                this.fileLoading = true;
                try {
                    const formData = new FormData();
                    formData.append('file', this.selectedFile);
                    const res = await fetch(`${BASE_URL}/attachments`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                        body: formData
                    });
                    if (res.ok) {
                        this.selectedFile = null;
                        if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                        window.location.reload();
                    } else {
                        const data = await res.json();
                        alert(data.message || 'Failed to upload file');
                    }
                } catch (e) { alert('Error uploading file'); }
                this.fileLoading = false;
            },

            async addExpense() {
                this.expenseLoading = true;
                try {
                    const res = await fetch(`${BASE_URL}/expenses`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                        body: JSON.stringify(this.newExpense)
                    });
                    const data = await res.json();
                    if (res.ok) {
                        appendExpenseRow(data.expense);
                        updateExpensesCount(1);
                        updateBudgetSummary(data.project_actual_cost, data.budget_used_percent);
                        this.newExpense = { title: '', amount: '', expense_date: '', category: 'general', description: '' };
                        this.expenseAmountDisplay = '';
                    } else {
                        alert(data.message || 'Failed to record expense');
                    }
                } catch (e) { alert('Error recording expense'); }
                this.expenseLoading = false;
            }
        };
    }

    async function updateTaskStatus(taskId, status) {
        const borderMap = {
            todo: 'border-l-gray-400',
            in_progress: 'border-l-blue-500',
            review: 'border-l-yellow-500',
            done: 'border-l-green-500',
        };
        try {
            const res = await fetch(`${BASE_URL}/tasks/${taskId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                body: JSON.stringify({ status })
            });
            if (res.ok) {
                const row = document.getElementById(`task-${taskId}`);
                if (row) {
                    Object.values(borderMap).forEach(cls => row.classList.remove(cls));
                    row.classList.add(borderMap[status] ?? 'border-l-gray-300');

                    const title = row.querySelector('p.font-medium');
                    if (title) {
                        if (status === 'done') {
                            title.classList.add('line-through', 'text-gray-400');
                            title.classList.remove('text-gray-900');
                        } else {
                            title.classList.remove('line-through', 'text-gray-400');
                            title.classList.add('text-gray-900');
                        }
                    }
                }
            } else {
                const data = await res.json();
                alert(data.message || 'Failed to update task');
            }
        } catch (e) { alert('Error updating task'); }
    }

    async function deleteTask(taskId) {
        if (!confirm('Delete this task?')) return;
        try {
            const res = await fetch(`${BASE_URL}/tasks/${taskId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
            });
            if (res.ok) {
                const el = document.getElementById(`task-${taskId}`);
                if (el) el.remove();
            }
        } catch (e) { alert('Error deleting task'); }
    }

    async function toggleMilestone(milestoneId, complete) {
        const body = complete ? { mark_complete: true } : { mark_incomplete: true };
        try {
            const res = await fetch(`${BASE_URL}/milestones/${milestoneId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                body: JSON.stringify(body)
            });
            if (res.ok) window.location.reload();
        } catch (e) { alert('Error updating milestone'); }
    }

    async function deleteMilestone(milestoneId) {
        if (!confirm('Delete this milestone?')) return;
        try {
            const res = await fetch(`${BASE_URL}/milestones/${milestoneId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
            });
            if (res.ok) {
                const el = document.getElementById(`milestone-${milestoneId}`);
                if (el) {
                    el.remove();
                    updateCount('milestones-count-badge', -1);
                    ensureMilestoneEmptyState();
                }
            }
        } catch (e) { alert('Error deleting milestone'); }
    }

    async function deleteNote(noteId) {
        if (!confirm('Delete this note?')) return;
        try {
            const res = await fetch(`${BASE_URL}/notes/${noteId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
            });
            if (res.ok) {
                const el = document.getElementById(`note-${noteId}`);
                if (el) {
                    el.remove();
                    updateCount('notes-count-badge', -1);
                    ensureNotesEmptyState();
                }
            }
        } catch (e) { alert('Error deleting note'); }
    }

    async function deleteAttachment(attachmentId) {
        if (!confirm('Delete this file?')) return;
        try {
            const res = await fetch(`${BASE_URL}/attachments/${attachmentId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
            });
            if (res.ok) {
                const el = document.getElementById(`attachment-${attachmentId}`);
                if (el) el.remove();
            }
        } catch (e) { alert('Error deleting file'); }
    }

    async function invoiceMilestone(milestoneId) {
        if (!confirm('Create an accounting voucher for this milestone?')) return;
        try {
            const res = await fetch(`${BASE_URL}/milestones/${milestoneId}/invoice`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (res.ok && data.success) {
                alert(data.message || 'Milestone invoiced successfully.');
                window.location.reload();
            } else {
                alert(data.message || 'Failed to invoice milestone');
            }
        } catch (e) { alert('Error invoicing milestone'); }
    }

    async function deleteExpense(expenseId) {
        if (!confirm('Delete this expense? The accounting entry will also be reversed.')) return;
        try {
            const res = await fetch(`${BASE_URL}/expenses/${expenseId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
            });
            if (res.ok) {
                const el = document.getElementById(`expense-${expenseId}`);
                if (el) {
                    el.remove();
                    updateExpensesCount(-1);
                    ensureExpenseEmptyState();
                }
            }
        } catch (e) { alert('Error deleting expense'); }
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatCurrency(amount) {
        const numericAmount = Number(amount || 0);
        return `₦${numericAmount.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }

    function formatExpenseDate(dateValue) {
        if (!dateValue) return '';
        return new Date(dateValue).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
    }

    function formatExpenseCategory(category) {
        return String(category || 'general')
            .split('_')
            .map(part => part.charAt(0).toUpperCase() + part.slice(1))
            .join(' ');
    }

    function updateCount(badgeId, delta) {
        const badge = document.getElementById(badgeId);
        if (!badge) return;

        const nextValue = Math.max(0, (parseInt(badge.textContent, 10) || 0) + delta);
        badge.textContent = nextValue;
    }

    function updateExpensesCount(delta) {
        updateCount('expenses-count-badge', delta);
    }

    function updateBudgetSummary(actualCost, budgetUsedPercent) {
        const amountEl = document.getElementById('budget-spent-amount');
        const percentEl = document.getElementById('budget-used-percent');

        if (amountEl) {
            amountEl.textContent = Number(actualCost || 0).toLocaleString('en-NG', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        }

        if (percentEl && budgetUsedPercent !== undefined && budgetUsedPercent !== null) {
            percentEl.textContent = budgetUsedPercent;
        }
    }

    function appendExpenseRow(expense) {
        const list = document.getElementById('expenses-list');
        if (!list || !expense) return;

        const emptyState = document.getElementById('expenses-empty-state');
        if (emptyState) emptyState.remove();

        const descriptionHtml = expense.description
            ? `<p class="text-xs text-gray-500 mt-1">${escapeHtml(expense.description)}</p>`
            : '';
        const creatorHtml = expense.creator?.name
            ? `<span class="text-xs text-gray-400">by ${escapeHtml(expense.creator.name)}</span>`
            : '';
        const voucherHtml = expense.voucher?.voucher_number
            ? `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">${escapeHtml(expense.voucher.voucher_number)}</span>`
            : '';

        const row = document.createElement('div');
        row.id = `expense-${expense.id}`;
        row.className = 'flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg';
        row.innerHTML = `
            <div class="flex items-center space-x-4 flex-1 min-w-0">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">${escapeHtml(expense.title)}</p>
                    <div class="flex items-center space-x-3 mt-1">
                        <span class="text-xs font-medium text-red-600">${formatCurrency(expense.amount)}</span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">${escapeHtml(formatExpenseCategory(expense.category))}</span>
                        <span class="text-xs text-gray-500">${escapeHtml(formatExpenseDate(expense.expense_date))}</span>
                        ${creatorHtml}
                        ${voucherHtml}
                    </div>
                    ${descriptionHtml}
                </div>
            </div>
            <button onclick="deleteExpense(${expense.id})" class="ml-3 p-1 text-gray-400 hover:text-red-500 transition-colors duration-200" title="Delete expense">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        `;

        list.prepend(row);
    }

    function appendMilestoneRow(milestone) {
        const list = document.getElementById('milestones-list');
        if (!list || !milestone) return;

        const emptyState = document.getElementById('milestones-empty-state');
        if (emptyState) emptyState.remove();

        const amountHtml = milestone.amount
            ? `<span class="text-xs font-medium ${milestone.is_billable ? 'text-violet-600' : 'text-gray-500'}">${formatCurrency(milestone.amount)}${milestone.is_billable ? ' (Billable)' : ''}</span>`
            : '';
        const dueDateHtml = milestone.due_date
            ? `<span class="text-xs text-gray-500">Due: ${escapeHtml(formatExpenseDate(milestone.due_date))}</span>`
            : '';

        const row = document.createElement('div');
        row.id = `milestone-${milestone.id}`;
        row.className = 'flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg';
        row.innerHTML = `
            <div class="flex items-center space-x-4 flex-1 min-w-0">
                <button onclick="toggleMilestone(${milestone.id}, true)"
                        class="flex-shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center transition-colors duration-200 border-gray-300 hover:border-green-400">
                </button>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">${escapeHtml(milestone.title)}</p>
                    <div class="flex items-center space-x-3 mt-1">
                        ${amountHtml}
                        ${dueDateHtml}
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-2 ml-3">
                <button onclick="deleteMilestone(${milestone.id})" class="p-1 text-gray-400 hover:text-red-500 transition-colors duration-200" title="Delete milestone">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        `;

        list.prepend(row);
    }

    function appendNoteRow(note) {
        const list = document.getElementById('notes-list');
        if (!list || !note) return;

        const emptyState = document.getElementById('notes-empty-state');
        if (emptyState) emptyState.remove();

        const initials = (note.user?.name || '?').substring(0, 2).toUpperCase();
        const internalHtml = note.is_internal
            ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-700">Internal</span>'
            : '';

        const row = document.createElement('div');
        row.id = `note-${note.id}`;
        row.className = 'flex space-x-3';
        row.innerHTML = `
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-violet-100 rounded-full flex items-center justify-center">
                    <span class="text-xs font-medium text-violet-600">${escapeHtml(initials)}</span>
                </div>
            </div>
            <div class="flex-1 bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-gray-900">${escapeHtml(note.user?.name || 'Unknown')}</span>
                        <span class="text-xs text-gray-400">Just now</span>
                        ${internalHtml}
                    </div>
                    <button onclick="deleteNote(${note.id})" class="p-1 text-gray-400 hover:text-red-500 transition-colors duration-200" title="Delete note">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-gray-700 whitespace-pre-line">${escapeHtml(note.content)}</p>
            </div>
        `;

        list.prepend(row);
    }

    function ensureExpenseEmptyState() {
        const list = document.getElementById('expenses-list');
        if (!list || list.children.length > 0) return;

        const emptyState = document.createElement('div');
        emptyState.id = 'expenses-empty-state';
        emptyState.className = 'text-center py-8 text-gray-400';
        emptyState.innerHTML = `
            <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <p>No expenses recorded yet. Add project costs above — they'll be posted to accounting automatically.</p>
        `;

        list.appendChild(emptyState);
    }

    function ensureMilestoneEmptyState() {
        const list = document.getElementById('milestones-list');
        if (!list || list.children.length > 0) return;

        const emptyState = document.createElement('div');
        emptyState.id = 'milestones-empty-state';
        emptyState.className = 'text-center py-8 text-gray-400';
        emptyState.innerHTML = `
            <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
            </svg>
            <p>No milestones yet. Add one above to track project deliverables.</p>
        `;

        list.appendChild(emptyState);
    }

    function ensureNotesEmptyState() {
        const list = document.getElementById('notes-list');
        if (!list || list.children.length > 0) return;

        const emptyState = document.createElement('div');
        emptyState.id = 'notes-empty-state';
        emptyState.className = 'text-center py-8 text-gray-400';
        emptyState.innerHTML = `
            <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
            </svg>
            <p>No notes yet. Add one to keep track of important information.</p>
        `;

        list.appendChild(emptyState);
    }
</script>
@endpush
