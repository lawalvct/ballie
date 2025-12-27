@extends('layouts.tenant')
@section('title', $article->title)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm">
            <li>
                <a href="{{ route('tenant.support.knowledge-base.index', ['tenant' => tenant()->slug]) }}"
                   class="text-pink-600 hover:text-pink-700">Knowledge Base</a>
            </li>
            <li class="text-gray-400">/</li>
            <li>
                <a href="{{ route('tenant.support.knowledge-base.category', ['tenant' => tenant()->slug, 'category' => $category->slug]) }}"
                   class="text-pink-600 hover:text-pink-700">{{ $category->name }}</a>
            </li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">{{ Str::limit($article->title, 50) }}</li>
        </ol>
    </nav>

    <!-- Article -->
    <article class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        @if($article->featured_image)
            <img src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}"
                 class="w-full h-96 object-cover">
        @endif

        <div class="p-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center space-x-2 mb-3">
                    <span class="px-3 py-1 bg-pink-100 text-pink-700 text-sm font-semibold rounded">
                        {{ $category->name }}
                    </span>
                    @if($article->is_featured)
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-sm font-semibold rounded">
                            Featured
                        </span>
                    @endif
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $article->title }}</h1>
                <div class="flex items-center space-x-4 text-sm text-gray-500">
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $article->published_at->format('M d, Y') }}
                    </span>
                    <span>•</span>
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        {{ $article->view_count }} views
                    </span>
                    <span>•</span>
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $article->reading_time }} min read
                    </span>
                </div>
            </div>

            <!-- Content -->
            <div class="prose max-w-none mb-8">
                {!! nl2br(e($article->content)) !!}
            </div>

            <!-- Feedback -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Was this article helpful?</h3>
                <div id="feedback-buttons" class="flex items-center space-x-4">
                    <button onclick="submitFeedback(true)"
                            class="flex items-center space-x-2 px-6 py-3 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg font-medium transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                        </svg>
                        <span>Yes, helpful</span>
                    </button>
                    <button onclick="submitFeedback(false)"
                            class="flex items-center space-x-2 px-6 py-3 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg font-medium transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5" />
                        </svg>
                        <span>No, not helpful</span>
                    </button>
                </div>
                <div id="feedback-message" class="hidden mt-4 p-4 bg-pink-50 border border-pink-200 rounded-lg">
                    <p class="text-pink-700 font-medium"></p>
                </div>
                @if($article->helpful_count > 0)
                    <p class="text-sm text-gray-500 mt-4">
                        {{ $article->helpfulness_percentage }}% of readers found this helpful
                        ({{ $article->helpful_count }} out of {{ $article->helpful_count + $article->not_helpful_count }})
                    </p>
                @endif
            </div>
        </div>
    </article>

    <!-- Related Articles -->
    @if($relatedArticles->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Related Articles</h2>
            <div class="space-y-3">
                @foreach($relatedArticles as $related)
                    <a href="{{ route('tenant.support.knowledge-base.article', ['tenant' => tenant()->slug, 'category' => $category->slug, 'article' => $related->slug]) }}"
                       class="block p-4 hover:bg-gray-50 rounded-lg transition-colors">
                        <h3 class="font-semibold text-gray-900 mb-1">{{ $related->title }}</h3>
                        <p class="text-sm text-gray-600 line-clamp-1">{{ $related->excerpt }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- CTA -->
    <div class="mt-8 bg-gradient-to-r from-pink-500 to-purple-600 rounded-lg shadow-xl p-8 text-center text-white">
        <h2 class="text-xl font-bold mb-2">Still need help?</h2>
        <p class="text-pink-100 mb-4">Our support team is here to assist you</p>
        <a href="{{ route('tenant.support.create', ['tenant' => tenant()->slug]) }}"
           class="inline-block bg-white text-pink-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
            Create Support Ticket
        </a>
    </div>
</div>

@push('scripts')
<script>
function submitFeedback(helpful) {
    const buttons = document.getElementById('feedback-buttons');
    const message = document.getElementById('feedback-message');

    fetch('{{ route("tenant.support.knowledge-base.helpful", ["tenant" => tenant()->slug, "article" => $article->id]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ helpful: helpful })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            buttons.classList.add('hidden');
            message.classList.remove('hidden');
            message.querySelector('p').textContent = data.message;
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
@endpush
@endsection
