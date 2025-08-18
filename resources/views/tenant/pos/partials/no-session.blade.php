<div class="flex items-center justify-center min-h-[60vh]">
    <div class="text-center p-8 bg-white/60 dark:bg-gray-800/40 backdrop-blur-sm rounded-2xl border border-gray-200/80 dark:border-gray-700/50 shadow-xl">
        <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-cash-register text-gray-400 dark:text-gray-500 text-3xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">No Active Session</h2>
        <p class="text-gray-600 dark:text-gray-300 mb-6">Please open a cash register session to start selling</p>
        <a href="{{ route('tenant.pos.register-session', ['tenant' => $tenant->slug]) }}"
           class="px-6 py-3 rounded-lg font-semibold btn-primary shadow-lg">
            Open Cash Register Session
        </a>
    </div>
</div>
