{{-- Reusable Data Table Component --}}
<div class="bg-white shadow overflow-hidden sm:rounded-md">
    {{-- Table Header with Search and Actions --}}
    @if(isset($showHeader) && $showHeader)
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    @if(isset($tableTitle))
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            {{ $tableTitle }}
                        </h3>
                    @endif
                    @if(isset($tableSubtitle))
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            {{ $tableSubtitle }}
                        </p>
                    @endif
                </div>

                @if(isset($headerActions))
                    <div class="flex space-x-3">
                        {{ $headerActions }}
                    </div>
                @endif
            </div>

            {{-- Search and Filters --}}
            @if(isset($showFilters) && $showFilters)
                <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="max-w-lg w-full lg:max-w-xs">
                            <label for="search" class="sr-only">Search</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input id="search" name="search" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="Search..." type="search">
                            </div>
                        </div>
                    </div>

                    @if(isset($filters))
                        <div class="mt-4 sm:mt-0 flex space-x-3">
                            {{ $filters }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif

    {{-- Table Content --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            @if(isset($columns))
                <thead class="bg-gray-50">
                    <tr>
                        @if(isset($showBulkActions) && $showBulkActions)
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Select</span>
                                <input type="checkbox" class="absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            </th>
                        @endif

                        @foreach($columns as $column)
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider {{ $column['class'] ?? '' }}">
                                @if(isset($column['sortable']) && $column['sortable'])
                                    <button class="group inline-flex">
                                        {{ $column['label'] }}
                                        <span class="ml-2 flex-none rounded text-gray-400 group-hover:text-gray-500">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                            </svg>
                                        </span>
                                    </button>
                                @else
                                    {{ $column['label'] }}
                                @endif
                            </th>
                        @endforeach

                        @if(isset($showActions) && $showActions)
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        @endif
                    </tr>
                </thead>
            @endif

            <tbody class="bg-white divide-y divide-gray-200">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if(isset($pagination))
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $pagination }}
        </div>
    @endif
</div>
