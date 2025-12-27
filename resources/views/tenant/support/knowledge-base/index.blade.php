@extends('layouts.tenant')

@section('title', 'Knowledge Base')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Knowledge Base</h1>
        <p class="text-xl text-gray-600">Find answers to common questions</p>

        <!-- Search -->
        <form method="GET" action="{{ route('tenant.support.search', ['tenant' => tenant()->slug]) }}" class="mt-8 max-w-2xl mx-auto">
            <div class="relative">
                <input type="text" name="q" placeholder="Search articles and tickets..." required minlength="3"
                       class="w-full px-6 py-4 pr-12 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent text-lg">
                <button type="submit" class="absolute right-4 top-1/2 transform -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <!-- Categories -->
    @if($categories->count() > 0)
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Browse by Category</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('tenant.support.knowledge-base.category', ['tenant' => tenant()->slug, 'category' => $category->slug]) }}"
                       class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 border-2 border-transparent hover:border-pink-500">
                        <div class="flex items-start space-x-4">
                            @if($category->icon)
                                <div class="flex-shrink-0 w-12 h-12 rounded-lg flex items-center justify-center"
                                     style="background-color: {{ $category->color }}20;">
                                    <span class="text-2xl">{{ $category->icon }}</span>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $category->name }}</h3>
                                @if($category->description)
                                    <p class="text-sm text-gray-600 mb-3">{{ $category->description }}</p>
                                @endif
                                <p class="text-sm text-pink-600 font-medium">
                                    {{ $category->articles_count }} {{ Str::plural('article', $category->articles_count) }}
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Featured Articles -->
    @if($featuredArticles->count() > 0)
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Featured Articles</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredArticles as $article)
                    <a href="{{ route('tenant.support.knowledge-base.article', ['tenant' => tenant()->slug, 'category' => $article->category->slug, 'article' => $article->slug]) }}"
                       class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                        @if($article->featured_image)
                            <img src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}"
                                 class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gradient-to-br from-pink-400 to-purple-500 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-white opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                        @endif
                        <div class="p-6">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="px-2 py-1 bg-pink-100 text-pink-700 text-xs font-semibold rounded">Featured</span>
                                <span class="text-xs text-gray-500">{{ $article->category->name }}</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">{{ $article->title }}</h3>
                            <p class="text-sm text-gray-600 line-clamp-2 mb-3">{{ $article->excerpt }}</p>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>{{ $article->view_count }} views</span>
                                <span>{{ $article->reading_time }} min read</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Popular Articles -->
    @if($popularArticles->count() > 0)
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Popular Articles</h2>
            <div class="bg-white rounded-lg shadow-md divide-y">
                @foreach($popularArticles as $article)
                    <a href="{{ route('tenant.support.knowledge-base.article', ['tenant' => tenant()->slug, 'category' => $article->category->slug, 'article' => $article->slug]) }}"
                       class="block p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $article->title }}</h3>
                                <p class="text-sm text-gray-600 mb-2 line-clamp-1">{{ $article->excerpt }}</p>
                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                    <span>{{ $article->category->name }}</span>
                                    <span>•</span>
                                    <span>{{ $article->view_count }} views</span>
                                    <span>•</span>
                                    <span>{{ $article->reading_time }} min read</span>
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

    <!-- CTA -->
    <div class="bg-gradient-to-r from-pink-500 to-purple-600 rounded-lg shadow-xl p-8 text-center text-white">
        <h2 class="text-2xl font-bold mb-2">Can't find what you're looking for?</h2>
        <p class="text-pink-100 mb-6">Our support team is here to help</p>
        <a href="{{ route('tenant.support.create', ['tenant' => tenant()->slug]) }}"
           class="inline-block bg-white text-pink-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
            Create Support Ticket
        </a>
    </div>
</div>
@endsection
