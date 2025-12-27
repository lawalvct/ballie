@extends('layouts.tenant')

@section('title', 'Search Results')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Search Results</h1>

        <!-- Search Bar -->
        <form method="GET" action="{{ route('tenant.support.search', ['tenant' => tenant()->slug]) }}">
            <div class="relative">
                <input type="text" name="q" value="{{ $query }}" placeholder="Search articles and tickets..." required minlength="3"
                       class="w-full px-6 py-3 pr-12 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                <button type="submit" class="absolute right-4 top-1/2 transform -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>
        </form>

        <p class="text-gray-600 mt-4">
            Found {{ $tickets->count() + $articles->count() }} results for "<span class="font-semibold">{{ $query }}</span>"
        </p>
    </div>

    @if($tickets->count() === 0 && $articles->count() === 0)
        <!-- No Results -->
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No results found</h3>
            <p class="text-gray-600 mb-6">Try different keywords or browse our knowledge base</p>
            <div class="flex justify-center space-x-4">
                <a href="{{ route('tenant.support.knowledge-base.index', ['tenant' => tenant()->slug]) }}"
                   class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                    Browse Knowledge Base
                </a>
                <a href="{{ route('tenant.support.create', ['tenant' => tenant()->slug]) }}"
                   class="bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 px-6 py-3 rounded-lg font-semibold transition-colors">
                    Create Ticket
                </a>
            </div>
        </div>
    @else
        <!-- Tickets Results -->
        @if($tickets->count() > 0)
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    Your Tickets ({{ $tickets->count() }})
                </h2>
                <div class="space-y-4">
                    @foreach($tickets as $ticket)
                        <a href="{{ route('tenant.support.tickets.show', ['tenant' => tenant()->slug, 'ticket' => $ticket->id]) }}"
                           class="block bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <span class="text-sm font-mono text-gray-500">#{{ $ticket->ticket_number }}</span>

                                        <!-- Status Badge -->
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            @if($ticket->status === 'new') bg-purple-100 text-purple-700
                                            @elseif($ticket->status === 'open') bg-blue-100 text-blue-700
                                            @elseif($ticket->status === 'in_progress') bg-yellow-100 text-yellow-700
                                            @elseif($ticket->status === 'waiting_customer') bg-orange-100 text-orange-700
                                            @elseif($ticket->status === 'resolved') bg-green-100 text-green-700
                                            @else bg-gray-100 text-gray-700
                                            @endif">
                                            {{ $ticket->status_label['text'] }}
                                        </span>

                                        <!-- Priority Badge -->
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            @if($ticket->priority === 'urgent') bg-red-100 text-red-700
                                            @elseif($ticket->priority === 'high') bg-orange-100 text-orange-700
                                            @elseif($ticket->priority === 'medium') bg-yellow-100 text-yellow-700
                                            @else bg-gray-100 text-gray-700
                                            @endif">
                                            {{ $ticket->priority_label['text'] }}
                                        </span>
                                    </div>

                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $ticket->subject }}</h3>

                                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                                        <span>{{ $ticket->category->name }}</span>
                                        <span>•</span>
                                        <span>{{ $ticket->created_at->diffForHumans() }}</span>
                                        <span>•</span>
                                        <span>{{ $ticket->reply_count }} {{ Str::plural('reply', $ticket->reply_count) }}</span>
                                    </div>
                                </div>

                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 ml-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Articles Results -->
        @if($articles->count() > 0)
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    Knowledge Base Articles ({{ $articles->count() }})
                </h2>
                <div class="space-y-4">
                    @foreach($articles as $article)
                        <a href="{{ route('tenant.support.knowledge-base.article', ['tenant' => tenant()->slug, 'category' => $article->category->slug, 'article' => $article->slug]) }}"
                           class="block bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="px-2 py-1 bg-pink-100 text-pink-700 text-xs font-semibold rounded">
                                            {{ $article->category->name }}
                                        </span>
                                        @if($article->is_featured)
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded">
                                                Featured
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $article->title }}</h3>
                                    <p class="text-gray-600 mb-3 line-clamp-2">{{ $article->excerpt }}</p>

                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <span>{{ $article->view_count }} views</span>
                                        <span>•</span>
                                        <span>{{ $article->reading_time }} min read</span>
                                        @if($article->helpful_count > 0)
                                            <span>•</span>
                                            <span class="text-green-600">{{ $article->helpfulness_percentage }}% helpful</span>
                                        @endif
                                    </div>
                                </div>

                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 ml-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    <!-- Additional Help -->
    <div class="bg-gray-50 rounded-lg p-6 text-center">
        <p class="text-gray-700 mb-4">Can't find what you're looking for?</p>
        <a href="{{ route('tenant.support.create', ['tenant' => tenant()->slug]) }}"
           class="inline-block bg-pink-500 hover:bg-pink-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
            Create Support Ticket
        </a>
    </div>
</div>
@endsection
