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
                { id: 'getting-started', title: 'Getting Started', component: 'getting-started' },
                {
                    id: 'modules',
                    title: 'Modules',
                    submenu: [
                        { id: 'accounting', title: 'Accounting', component: 'module-accounting' },
                        { id: 'inventory', title: 'Inventory', component: 'module-inventory' },
                        { id: 'crm', title: 'CRM', component: 'module-crm' },
                        { id: 'pos', title: 'POS', component: 'module-pos' },
                        { id: 'payroll', title: 'Payroll', component: 'module-payroll' },
                        { id: 'admin', title: 'Admin', component: 'module-admin' }
                    ]
                },
                { id: 'faq', title: 'FAQ', component: 'faq-section' },
                { id: 'support', title: 'Support', component: 'support-section' }
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
        'module-accounting': {
            template: `
                <div>
                    <h1 class="text-3xl font-bold mb-4">Accounting Module</h1>
                    <p class="text-gray-700 mb-4">Manage your financial transactions, invoices, and reports.</p>
                    <div class="space-y-4">
                        <div class="border-l-4 border-green-500 pl-4">
                            <h3 class="font-semibold text-lg mb-2">Creating Invoices</h3>
                            <p class="text-gray-700">Learn how to create and manage invoices for your customers.</p>
                        </div>
                        <div class="border-l-4 border-green-500 pl-4">
                            <h3 class="font-semibold text-lg mb-2">Managing Vouchers</h3>
                            <p class="text-gray-700">Record journal entries and financial transactions.</p>
                        </div>
                    </div>
                </div>
            `
        },
        'module-inventory': {
            template: `
                <div>
                    <h1 class="text-3xl font-bold mb-4">Inventory Module</h1>
                    <p class="text-gray-700 mb-4">Track and manage your stock levels and products.</p>
                    <div class="space-y-4">
                        <div class="border-l-4 border-purple-500 pl-4">
                            <h3 class="font-semibold text-lg mb-2">Product Management</h3>
                            <p class="text-gray-700">Add and organize your products and services.</p>
                        </div>
                        <div class="border-l-4 border-purple-500 pl-4">
                            <h3 class="font-semibold text-lg mb-2">Stock Tracking</h3>
                            <p class="text-gray-700">Monitor stock movements and inventory levels.</p>
                        </div>
                    </div>
                </div>
            `
        },
        'module-crm': {
            template: `
                <div>
                    <h1 class="text-3xl font-bold mb-4">CRM Module</h1>
                    <p class="text-gray-700 mb-4">Manage customer and vendor relationships.</p>
                    <div class="space-y-4">
                        <div class="border-l-4 border-pink-500 pl-4">
                            <h3 class="font-semibold text-lg mb-2">Customer Management</h3>
                            <p class="text-gray-700">Add and track customer information and transactions.</p>
                        </div>
                    </div>
                </div>
            `
        },
        'module-pos': {
            template: `
                <div>
                    <h1 class="text-3xl font-bold mb-4">POS Module</h1>
                    <p class="text-gray-700 mb-4">Process sales transactions quickly and efficiently.</p>
                </div>
            `
        },
        'module-payroll': {
            template: `
                <div>
                    <h1 class="text-3xl font-bold mb-4">Payroll Module</h1>
                    <p class="text-gray-700 mb-4">Manage employee salaries and payroll processing.</p>
                </div>
            `
        },
        'module-admin': {
            template: `
                <div>
                    <h1 class="text-3xl font-bold mb-4">Admin Module</h1>
                    <p class="text-gray-700 mb-4">Manage users, roles, and system permissions.</p>
                </div>
            `
        }
    }
}).mount('#helpApp');
</script>
@endpush
