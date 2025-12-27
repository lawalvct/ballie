@extends('layouts.tenant')

@section('title', $category->name . ' - Knowledge Base')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm">
            <li>
                <a href="{{ route('tenant.support.knowledge-base.index', ['tenant' => tenant()->slug]) }}"
                   class="text-pink-600 hover:text-pink-700">Knowledge Base</a>
            </li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">{{ $category->name }}</li>
        </ol>
    </nav>

    <!-- Category Header -->
    <div class="bg-white rounded-lg shadow-md p-8 mb-8">
        <div class="flex items-start space-x-4">
            @if($category->icon)
                <div class="flex-shrink-0 w-16 h-16 rounded-lg flex items-center justify-center"
                     style="background-color: {{ $category->color }}20;">
                    <span class="text-4xl">{{ $category->icon }}</span>
                </div>
            @endif
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $category->name }}</h1>
                @if($category->description)
                    <p class="text-gray-600">{{ $category->description }}</p>
                @endif
                <p class="text-sm text-gray-500 mt-2">{{ $articles->total() }} {{ Str::plural('article', $articles->total()) }}</p>
            </div>
        </div>
    </div>

    @if($articles->count() > 0)
        <!-- Articles List -->
        <div class="space-y-4">
            @foreach($articles as $article)
                <a href="{{ route('tenant.support.knowledge-base.article', ['tenant' => tenant()->slug, 'category' => $category->slug, 'article' => $article->slug]) }}"
                   class="block bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $article->title }}</h3>
                            <p class="text-gray-600 mb-3 line-clamp-2">{{ $article->excerpt }}</p>
                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                <span class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{ $article->view_count }} views
                                </span>
                                <span class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $article->reading_time }} min read
                                </span>
                                @if($article->helpful_count > 0)
                                    <span class="flex items-center text-green-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                        </svg>
                                        {{ $article->helpfulness_percentage }}% helpful
                                    </span>
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

        <!-- Pagination -->
        <div class="mt-8">
            {{ $articles->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No articles yet</h3>
            <p class="text-gray-600 mb-6">Articles for this category are coming soon</p>
            <a href="{{ route('tenant.support.knowledge-base.index', ['tenant' => tenant()->slug]) }}"
               class="text-pink-600 hover:text-pink-700 font-medium">
                ← Back to Knowledge Base
            </a>
        </div>
    @endif
</div>
@endsection
