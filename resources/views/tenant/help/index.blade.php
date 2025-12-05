@extends('layouts.tenant')

@section('title', 'Help & Documentation')
@section('page-title', 'Help & Documentation')
@section('page-description', 'Find guides, FAQs, and support to help you get the most out of Ballie.')


@push('styles')
<style>
[v-cloak] { display: none; }
.help-sidebar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.menu-item { cursor: pointer; transition: all 0.2s; color: rgba(255,255,255,0.9); }
.menu-item:hover { background: rgba(255,255,255,0.15); }
.menu-item.active { background: rgba(255,255,255,0.25); color: white; font-weight: 600; }
.submenu-item { cursor: pointer; padding-left: 2rem; color: rgba(255,255,255,0.85); }
.submenu-item:hover { background: rgba(255,255,255,0.1); }
.submenu-item.active { background: rgba(255,255,255,0.2); color: white; font-weight: 500; }
.help-sidebar h2 { color: white; }
.mobile-menu-btn { display: none; }
@media (max-width: 768px) {
    .help-sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 280px; z-index: 50; transform: translateX(-100%); transition: transform 0.3s; }
    .help-sidebar.open { transform: translateX(0); }
    .help-content { width: 100%; }
    .mobile-menu-btn { display: block; }
    .mobile-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 40; }
    .mobile-overlay.show { display: block; }
}
</style>
@endpush

@section('content')
<div id="helpApp" v-cloak>
    <!-- Mobile Menu Button -->
    <button @click="toggleSidebar" class="mobile-menu-btn mb-4 bg-purple-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        Menu
    </button>

    <!-- Mobile Overlay -->
    <div @click="closeSidebar" class="mobile-overlay" :class="{ 'show': sidebarOpen }"></div>

    <div class="flex gap-6">
        <!-- Sidebar -->
        <div class="help-sidebar rounded-lg shadow p-4 md:w-72" :class="{ 'open': sidebarOpen }">
        <h2 class="text-xl font-bold mb-4">Documentation</h2>
        <nav>
            <div v-for="menu in menus" :key="menu.id" class="mb-2">
                <div @click="toggleMenu(menu.id)"
                     class="menu-item px-3 py-2 rounded flex items-center justify-between"
                     :class="{ 'active': activeMenu === menu.id && !menu.submenu }">
                    <span v-text="menu.title"></span>
                    <svg v-if="menu.submenu" class="w-4 h-4 transition-transform text-white"
                         :class="{ 'rotate-90': openMenus.includes(menu.id) }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
                <div v-if="menu.submenu && openMenus.includes(menu.id)" class="mt-1">
                    <div v-for="sub in menu.submenu" :key="sub.id"
                         @click="selectSubmenu(menu.id, sub.id)"
                         class="submenu-item px-3 py-2 rounded text-sm"
                         :class="{ 'active': activeMenu === menu.id && activeSubmenu === sub.id }"
                         v-text="sub.title">
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <!-- Content Area -->
    <div class="help-content bg-white rounded-lg shadow p-4 md:p-8 flex-1">
        <component :is="currentComponent"></component>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/vue@3.3.4/dist/vue.global.prod.js"></script>
<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            sidebarOpen: false,
            activeMenu: 'getting-started',
            activeSubmenu: null,
            openMenus: ['getting-started'],
            menus: [
                { id: 'getting-started', title: '🚀 Getting Started', component: 'getting-started' },
                {
                    id: 'accounting',
                    title: '💰 Accounting',
                    submenu: [
                        { id: 'invoices', title: 'Invoices & Quotations', component: 'accounting-invoices' },
                        { id: 'vouchers', title: 'Vouchers & Journal Entries', component: 'accounting-vouchers' },
                        { id: 'ledger', title: 'Chart of Accounts', component: 'accounting-ledger' },
                        { id: 'banking', title: 'Banking & Reconciliation', component: 'accounting-banking' },
                        { id: 'reports', title: 'Financial Reports', component: 'accounting-reports' }
                    ]
                },
                {
                    id: 'inventory',
                    title: '📦 Inventory',
                    submenu: [
                        { id: 'products', title: 'Product Management', component: 'inventory-products' },
                        { id: 'stock', title: 'Stock Management', component: 'inventory-stock' },
                        { id: 'categories', title: 'Categories & Units', component: 'inventory-categories' },
                        { id: 'reports', title: 'Inventory Reports', component: 'inventory-reports' }
                    ]
                },
                {
                    id: 'crm',
                    title: '👥 CRM',
                    submenu: [
                        { id: 'customers', title: 'Customer Management', component: 'crm-customers' },
                        { id: 'vendors', title: 'Vendor Management', component: 'crm-vendors' },
                        { id: 'activities', title: 'Activities & Follow-ups', component: 'crm-activities' }
                    ]
                },
                {
                    id: 'payroll',
                    title: '💼 Payroll',
                    submenu: [
                        { id: 'employees', title: 'Employee Management', component: 'payroll-employees' },
                        { id: 'salary', title: 'Salary Components', component: 'payroll-salary' },
                        { id: 'processing', title: 'Payroll Processing', component: 'payroll-processing' },
                        { id: 'pension', title: 'Pension & PFA', component: 'payroll-pension' },
                        { id: 'attendance', title: 'Attendance & Leave', component: 'payroll-attendance' },
                        { id: 'loans', title: 'Loans & Advances', component: 'payroll-loans' }
                    ]
                },
                { id: 'pos', title: '🛒 Point of Sale', component: 'module-pos' },
                { id: 'statutory', title: '📋 Statutory & Tax', component: 'module-statutory' },
                { id: 'reports', title: '📊 Reports', component: 'module-reports' },
                { id: 'admin', title: '⚙️ Admin & Settings', component: 'module-admin' },
                { id: 'faq', title: '❓ FAQ', component: 'faq-section' },
                { id: 'support', title: '💬 Support', component: 'support-section' }
            ]
        }
    },
    computed: {
        currentComponent() {
            if (this.activeSubmenu) {
                const menu = this.menus.find(m => m.id === this.activeMenu);
                const sub = menu?.submenu?.find(s => s.id === this.activeSubmenu);
                return sub?.component || 'getting-started';
            }
            const menu = this.menus.find(m => m.id === this.activeMenu);
            return menu?.component || 'getting-started';
        }
    },
    methods: {
        toggleMenu(menuId) {
            const menu = this.menus.find(m => m.id === menuId);
            if (menu.submenu) {
                const index = this.openMenus.indexOf(menuId);
                if (index > -1) {
                    this.openMenus.splice(index, 1);
                } else {
                    this.openMenus.push(menuId);
                }
            } else {
                this.activeMenu = menuId;
                this.activeSubmenu = null;
            }
        },
        selectSubmenu(menuId, subId) {
            this.activeMenu = menuId;
            this.activeSubmenu = subId;
            this.closeSidebar();
        },
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
        },
        closeSidebar() {
            this.sidebarOpen = false;
        }
    },
    components: {
        @include('tenant.help.components.getting-started')
        @include('tenant.help.components.faq-section')
        @include('tenant.help.components.support-section')
        @include('tenant.help.components.accounting-invoices')
        @include('tenant.help.components.accounting-vouchers')
        @include('tenant.help.components.accounting-ledger')
        @include('tenant.help.components.accounting-banking')
        
        'accounting-reports': { template: `<div><h1 class="text-3xl font-bold mb-4">📊 Financial Reports</h1><p class="text-gray-600 mb-6">Generate P&L, Balance Sheet, Trial Balance, and Cash Flow reports.</p><div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6"><p class="text-sm text-yellow-700">🚧 Documentation coming soon...</p></div></div>` },
        
        // Inventory Components
        'inventory-products': { template: `<div><h1 class="text-3xl font-bold mb-4">📦 Product Management</h1><p class="text-gray-600 mb-6">Add, edit, and organize your products and services.</p><div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6"><p class="text-sm text-yellow-700">🚧 Documentation coming soon...</p></div></div>` },
        'inventory-stock': { template: `<div><h1 class="text-3xl font-bold mb-4">📊 Stock Management</h1><p class="text-gray-600 mb-6">Track stock movements, adjustments, and physical stock verification.</p><div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6"><p class="text-sm text-yellow-700">🚧 Documentation coming soon...</p></div></div>` },
        'inventory-categories': { template: `<div><h1 class="text-3xl font-bold mb-4">📊 Categories & Units</h1><p class="text-gray-600 mb-6">Organize products into categories and define measurement units.</p><div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6"><p class="text-sm text-yellow-700">🚧 Documentation coming soon...</p></div></div>` },
        'inventory-reports': { template: `<div><h1 class="text-3xl font-bold mb-4">📊 Inventory Reports</h1><p class="text-gray-600 mb-6">Generate stock reports, valuation, and movement analysis.</p><div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6"><p class="text-sm text-yellow-700">🚧 Documentation coming soon...</p></div></div>` },
        
        // CRM Components
        'crm-customers': { template: `<div><h1 class="text-3xl font-bold mb-4">👥 Customer Management</h1><p class="text-gray-600 mb-6">Manage customer information, statements, and transactions.</p><div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6"><p class="text-sm text-yellow-700">🚧 Documentation coming soon...</p></div></div>` },
        'crm-vendors': { template: `<div><h1 class="text-3xl font-bold mb-4">👥 Vendor Management</h1><p class="text-gray-600 mb-6">Manage vendor information and purchase transactions.</p><div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6"><p class="text-sm text-yellow-700">🚧 Documentation coming soon...</p></div></div>` },
        'crm-activities': { template: `<div><h1 class="text-3xl font-bold mb-4">📝 Activities & Follow-ups</h1><p class="text-gray-600 mb-6">Track customer activities and schedule follow-ups.</p><div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6"><p class="text-sm text-yellow-700">🚧 Documentation coming soon...</p></div></div>` },
        
        // Payroll Components
        'payroll-employees': { template: `<div><h1 class="text-3xl font-bold mb-4">👥 Employee Management</h1><p class="text-gray-600 mb-6">Add and manage employee records, departments, and positions.</p><div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6"><p class="text-sm text-yellow-700">🚧 Documentation coming soon...</p></div></div>` },
        'payroll-salary': { template: `<div><h1 class="text-3xl font-bold mb-4">💵 Salary Components</h1><p class="text-gray-600 mb-6">Configure earnings, deductions, and employer contributions.</p><div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6"><p class="text-sm text-yellow-700">🚧 Documentation coming soon...</p></div></div>` },
        'payroll-processing': { template: `<div><h1 class="text-3xl font-bold mb-4">⚙️ Payroll Processing</h1><p class="text-gray-600 mb-6">Process monthly payroll, generate payslips, and manage payments.</p><div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6"><p class="text-sm text-yellow-700">🚧 Documentation coming soon...</p></div></div>` },
        'payroll-pension': { template: `<div><h1 class="text-3xl font-bold mb-4">🏦 Pension & PFA</h1><p class="text-gray-600 mb-6">Manage pension contributions and PFA providers.</p><div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6"><p class="text-sm text-yellow-700">🚧 Documentation coming soon...</p></div></div>` },
        'payroll-attendance': { template: `<div><h1 class="text-3xl font-bold mb-4">📅 Attendance & Leave</h1><p class="text-gray-600 mb-6">Track employee attendance, leave requests, and overtime.</p><div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6"><p class="text-sm text-yellow-700">🚧 Documentation coming soon...</p></div></div>` },
        'payroll-loans': { template: `<div><h1 class="text-3xl font-bold mb-4">💰 Loans & Advances</h1><p class="text-gray-600 mb-6">Manage employee loans and salary advances.</p><div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6"><p class="text-sm text-yellow-700">🚧 Documentation coming soon...</p></div></div>` },
        
        // Other Modules
        'module-pos': { template: `<div><h1 class="text-3xl font-bold mb-4">🛒 Point of Sale</h1><p class="text-gray-600 mb-6">Process sales transactions, manage cash register, and print receipts.</p><div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6"><p class="text-sm text-yellow-700">🚧 Documentation coming soon...</p></div></div>` },
        'module-statutory': { template: `<div><h1 class="text-3xl font-bold mb-4">📋 Statutory & Tax</h1><p class="text-gray-600 mb-6">Manage VAT, PAYE tax, and pension contributions.</p><div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6"><p class="text-sm text-yellow-700">🚧 Documentation coming soon...</p></div></div>` },
        'module-reports': { template: `<div><h1 class="text-3xl font-bold mb-4">📊 Reports</h1><p class="text-gray-600 mb-6">Generate comprehensive business reports and analytics.</p><div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6"><p class="text-sm text-yellow-700">🚧 Documentation coming soon...</p></div></div>` },
        'module-admin': { template: `<div><h1 class="text-3xl font-bold mb-4">⚙️ Admin & Settings</h1><p class="text-gray-600 mb-6">Manage users, roles, permissions, and system settings.</p><div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6"><p class="text-sm text-yellow-700">🚧 Documentation coming soon...</p></div></div>` },
        'module-payroll': { template: `<div><h1 class="text-3xl font-bold mb-4">💼 Payroll Module</h1><p class="text-gray-600 mb-6">Manage employee salaries and payroll processing.</p><div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6"><p class="text-sm text-yellow-700">🚧 Documentation coming soon...</p></div></div>` },
        'module-crm': { template: `<div><h1 class="text-3xl font-bold mb-4">👥 CRM Module</h1><p class="text-gray-600 mb-6">Manage customer and vendor relationships.</p><div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6"><p class="text-sm text-yellow-700">🚧 Documentation coming soon...</p></div></div>` },
        'module-inventory': { template: `<div><h1 class="text-3xl font-bold mb-4">📦 Inventory Module</h1><p class="text-gray-600 mb-6">Track and manage your stock levels and products.</p><div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6"><p class="text-sm text-yellow-700">🚧 Documentation coming soon...</p></div></div>` },
        'module-accounting': { template: `<div><h1 class="text-3xl font-bold mb-4">💰 Accounting Module</h1><p class="text-gray-600 mb-6">Manage your financial transactions, invoices, and reports.</p><div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6"><p class="text-sm text-yellow-700">🚧 Documentation coming soon...</p></div></div>` }
    }
}).mount('#helpApp');
</script>
@endpush
