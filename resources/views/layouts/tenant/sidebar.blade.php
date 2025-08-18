<aside id="sidebar" class="sidebar sidebar-expanded text-white h-screen fixed shadow-2xl z-30 transform lg:transform-none transition-all duration-300">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between h-20 px-6 border-b border-white border-opacity-10">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-xl flex items-center justify-center shadow-lg">
                <span class="text-white font-bold text-lg">B</span>
            </div>
                        <div class="sidebar-title overflow-hidden whitespace-nowrap transition-opacity">
                <span class="text-xl font-bold bg-gradient-to-r from-white to-gray-200 bg-clip-text text-transparent">
                    {{ $tenant->name ?? 'Ballie' }}
                </span>
                <div class="text-xs text-gray-300 mt-1">Professional</div>
            </div>
        </div>
        <button id="sidebarCollapseBtn" class="p-2 rounded-lg hover:bg-white hover:bg-opacity-10 hidden lg:block transition-all duration-200 -mr-2 sidebar-collapse-btn">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
        </button>


        <button id="mobileSidebarClose" class="p-2 rounded-lg hover:bg-white hover:bg-opacity-10 lg:hidden block transition-all duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Sidebar Menu -->
    <nav class="py-6 overflow-y-auto h-[calc(100vh-10rem)] custom-scrollbar">
        <ul class="space-y-2 px-4">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('tenant.dashboard', ['tenant' => tenant()->slug]) }}"
                   class="menu-item flex items-center px-4 py-3 rounded-xl group {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}"
                   title="Dashboard">
                    <div class="flex-shrink-0 w-6 h-6 mr-4 text-yellow-400 group-hover:scale-110 transition-transform duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <span class="menu-title whitespace-nowrap font-medium">Dashboard</span>
                </a>
            </li>

            <!-- Accounting -->
            <li>
                <a href="{{ route('tenant.accounting.index', ['tenant' => tenant()->slug]) }}"
                   class="menu-item flex items-center px-4 py-3 rounded-xl group {{ request()->routeIs('tenant.accounting.*') ? 'active' : '' }}"
                   title="Accounting">
                    <div class="flex-shrink-0 w-6 h-6 mr-4 text-green-400 group-hover:scale-110 transition-transform duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="menu-title whitespace-nowrap font-medium">Accounting</span>
                </a>
            </li>


            <!-- Inventory -->
            <li>
                <a href="{{ route('tenant.inventory.index', ['tenant' => tenant()->slug]) }}"
                   class="menu-item flex items-center px-4 py-3 rounded-xl group {{ request()->routeIs('tenant.inventory.*') ? 'active' : '' }}"
                   title="Inventory">
                    <div class="flex-shrink-0 w-6 h-6 mr-4 text-purple-400 group-hover:scale-110 transition-transform duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <span class="menu-title whitespace-nowrap font-medium">Inventory</span>
                </a>
            </li>



            <!-- CRM -->
            <li>
                <a href="{{ route('tenant.crm.index', ['tenant' => tenant()->slug]) }}"
                   class="menu-item flex items-center px-4 py-3 rounded-xl group {{ request()->routeIs('tenant.crm.*') ? 'active' : '' }}"
                   title="Customer Relationship Management">
                    <div class="flex-shrink-0 w-6 h-6 mr-4 text-pink-400 group-hover:scale-110 transition-transform duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span class="menu-title whitespace-nowrap font-medium">CRM</span>
                </a>
            </li>



            <!-- POS -->
            <li>
                <a href="{{ route('tenant.pos.index', ['tenant' => tenant()->slug]) }}"
                   class="menu-item flex items-center px-4 py-3 rounded-xl group {{ request()->routeIs('tenant.pos.*') ? 'active' : '' }}"
                   title="Point of Sale">
                    <div class="flex-shrink-0 w-6 h-6 mr-4 text-cyan-400 group-hover:scale-110 transition-transform duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <span class="menu-title whitespace-nowrap font-medium">POS</span>
                </a>
            </li>

            <!-- Payroll -->
            <li>
                <a href="{{ route('tenant.payroll.index', ['tenant' => tenant()->slug]) }}"
                   class="menu-item flex items-center px-4 py-3 rounded-xl group {{ request()->routeIs('tenant.payroll.*') ? 'active' : '' }}"
                   title="Payroll Management">
                    <div class="flex-shrink-0 w-6 h-6 mr-4 text-emerald-400 group-hover:scale-110 transition-transform duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                    </div>
                    <span class="menu-title whitespace-nowrap font-medium">Payroll</span>
                </a>
            </li>

            <!-- Admin Management -->
            <li>
                <a href="{{ route('tenant.admin.index', ['tenant' => tenant()->slug]) }}"
                   class="menu-item flex items-center px-4 py-3 rounded-xl group {{ request()->routeIs('tenant.admin.*') ? 'active' : '' }}"
                   title="Admin Management">
                    <div class="flex-shrink-0 w-6 h-6 mr-4 text-blue-400 group-hover:scale-110 transition-transform duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-2.239" />
                        </svg>
                    </div>
                    <span class="menu-title whitespace-nowrap font-medium">Admin Management</span>
                </a>
            </li>

            <!-- Reports -->
            <li>
                <a href="{{ route('tenant.reports.index', ['tenant' => tenant()->slug]) }}"
                   class="menu-item flex items-center px-4 py-3 rounded-xl group {{ request()->routeIs('tenant.reports.*') ? 'active' : '' }}"
                   title="Reports & Analytics">
                    <div class="flex-shrink-0 w-6 h-6 mr-4 text-red-400 group-hover:scale-110 transition-transform duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <span class="menu-title whitespace-nowrap font-medium">Reports</span>
                </a>
            </li>

            <!-- Audit -->
            <li>
                <a href="#"
                   class="menu-item flex items-center px-4 py-3 rounded-xl group {{ request()->routeIs('tenant.audit.*') ? 'active' : '' }}"
                   title="Audit Trail">
                    <div class="flex-shrink-0 w-6 h-6 mr-4 text-indigo-400 group-hover:scale-110 transition-transform duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span class="menu-title whitespace-nowrap font-medium">Audit</span>
                </a>
            </li>

            <!-- Settings -->
            <li>
                <a href="{{ route('tenant.settings.index', ['tenant' => tenant()->slug]) }}"
                   class="menu-item flex items-center px-4 py-3 rounded-xl group {{ request()->routeIs('tenant.settings.*') ? 'active' : '' }}"
                   title="Settings & Configuration">
                    <div class="flex-shrink-0 w-6 h-6 mr-4 text-gray-400 group-hover:scale-110 transition-transform duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <span class="menu-title whitespace-nowrap font-medium">Settings</span>
                </a>
            </li>
        </ul>

        <!-- User Profile Section -->
        <div class="px-4 mt-8 pt-6 border-t border-white border-opacity-10">
            <div class="flex items-center space-x-3 p-3 rounded-xl bg-white bg-opacity-5 hover:bg-opacity-10 transition-all duration-200">
                <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center text-white font-semibold text-sm shadow-lg">
                    {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                </div>
                <div class="sidebar-title overflow-hidden">
                    <div class="text-sm font-medium text-white truncate">{{ auth()->user()->name ?? 'User' }}</div>
                    <div class="text-xs text-gray-300 truncate">{{ auth()->user()->email ?? 'user@example.com' }}</div>
                </div>
            </div>
        </div>

        <!-- Logout -->
        <div class="px-4 mt-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="menu-item w-full flex items-center px-4 py-3 rounded-xl group hover:bg-red-500 hover:bg-opacity-20 transition-all duration-200">
                    <div class="flex-shrink-0 w-6 h-6 mr-4 text-red-400 group-hover:scale-110 transition-transform duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <span class="menu-title whitespace-nowrap font-medium">Logout</span>
                </button>
            </form>
        </div>
    </nav>
</aside>
