<nav class="flex-1 px-6 py-6 space-y-2 overflow-y-auto custom-scrollbar">
                <!-- Main Navigation -->
                <div class="space-y-1">
                    <a href="{{ route('super-admin.dashboard') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-300 {{ request()->routeIs('super-admin.dashboard') ? 'nav-item-active text-white shadow-lg' : 'text-gray-300 nav-item-hover hover:text-white' }}"
                       title="Dashboard">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                        </svg>
                        <span class="menu-title whitespace-nowrap">Dashboard</span>
                    </a>

                    <a href="{{ route('super-admin.tenants.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-300 {{ request()->routeIs('super-admin.tenants.*') ? 'nav-item-active text-white shadow-lg' : 'text-gray-300 nav-item-hover hover:text-white' }}"
                       title="Companies">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="menu-title whitespace-nowrap">Companies</span>
                    </a>

                    <a href="{{ route('super-admin.support.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-300 {{ request()->routeIs('super-admin.support.*') ? 'nav-item-active text-white shadow-lg' : 'text-gray-300 nav-item-hover hover:text-white' }}"
                       title="Support Center">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        <span class="menu-title whitespace-nowrap">Support Center</span>
                    </a>

                    {{-- <a href="#" class="flex items-center px-4 py-3 text-sm font-medium text-gray-300 rounded-lg nav-item-hover hover:text-white transition-all duration-300"
                       title="Revenue & Billing">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <span class="menu-title whitespace-nowrap">Revenue & Billing</span>
                    </a> --}}

                    {{-- <a href="#" class="flex items-center px-4 py-3 text-sm font-medium text-gray-300 rounded-lg nav-item-hover hover:text-white transition-all duration-300"
                       title="Analytics & Reports">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span class="menu-title whitespace-nowrap">Analytics & Reports</span>
                    </a> --}}
                </div>
                    <a href="{{ route('super-admin.emails.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-300 {{ request()->routeIs('super-admin.emails.*') ? 'nav-item-active text-white shadow-lg' : 'text-gray-300 nav-item-hover hover:text-white' }}"
                       title="Email Management">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="menu-title whitespace-nowrap">Email Management</span>
                    </a>

                    <a href="{{ route('super-admin.backups.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-300 {{ request()->routeIs('super-admin.backups.*') ? 'nav-item-active text-white shadow-lg' : 'text-gray-300 nav-item-hover hover:text-white' }}"
                       title="Backup Management">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                        <span class="menu-title whitespace-nowrap">Backup Management</span>
                    </a>

                    <a href="{{ route('super-admin.affiliates.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-300 {{ request()->routeIs('super-admin.affiliates.*') || request()->routeIs('super-admin.affiliate-*') ? 'nav-item-active text-white shadow-lg' : 'text-gray-300 nav-item-hover hover:text-white' }}"
                       title="Affiliate Program">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span class="menu-title whitespace-nowrap">Affiliate Program</span>
                    </a>

                <!-- System Section -->
                <div class="pt-6 mt-6 border-t border-white border-opacity-20">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 sidebar-section-title">System Management</p>

                    <div class="space-y-1">
                        <a href="#" class="flex items-center px-4 py-3 text-sm font-medium text-gray-300 rounded-lg nav-item-hover hover:text-white transition-all duration-300"
                           title="System Settings">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="menu-title whitespace-nowrap">System Settings</span>
                        </a>

                        <a href="#" class="flex items-center px-4 py-3 text-sm font-medium text-gray-300 rounded-lg nav-item-hover hover:text-white transition-all duration-300"
                           title="Security & Logs">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <span class="menu-title whitespace-nowrap">Security & Logs</span>
                        </a>
                    </div>
                </div>
            </nav>

