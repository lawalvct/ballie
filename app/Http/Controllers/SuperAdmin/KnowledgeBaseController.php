<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBaseArticle;
use App\Models\SupportCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KnowledgeBaseController extends Controller
{
    /**
     * Display a listing of knowledge base articles.
     */
    public function index(Request $request)
    {
        $query = KnowledgeBaseArticle::with('category')->latest();

        // Filters
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->published();
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        if ($request->filled('featured')) {
            $query->where('is_featured', $request->featured === 'yes');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $articles = $query->paginate(20);
        $categories = SupportCategory::active()->ordered()->get();

        return view('super-admin.support.kb.index', compact('articles', 'categories'));
    }

    /**
     * Show the form for creating a new article.
     */
    public function create()
    {
        $categories = SupportCategory::active()->ordered()->get();

        return view('super-admin.support.kb.create', compact('categories'));
    }

    /**
     * Store a newly created article in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:support_categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:knowledge_base_articles,slug',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('knowledge-base', 'public');
            $validated['featured_image'] = $path;
        }

        $validated['is_published'] = $request->has('is_published');
        $validated['is_featured'] = $request->has('is_featured');

        // Set published_at if publishing
        if ($validated['is_published'] && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        KnowledgeBaseArticle::create($validated);

        return redirect()
            ->route('super-admin.support.kb.index')
            ->with('success', 'Knowledge base article created successfully!');
    }

    /**
     * Show the form for editing the specified article.
     */
    public function edit(KnowledgeBaseArticle $article)
    {
        $categories = SupportCategory::active()->ordered()->get();

        return view('super-admin.support.kb.edit', compact('article', 'categories'));
    }

    /**
     * Update the specified article in storage.
     */
    public function update(Request $request, KnowledgeBaseArticle $article)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:support_categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:knowledge_base_articles,slug,' . $article->id,
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'remove_image' => 'boolean',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle featured image removal
        if ($request->has('remove_image') && $article->featured_image) {
            Storage::disk('public')->delete($article->featured_image);
            $validated['featured_image'] = null;
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }

            $path = $request->file('featured_image')->store('knowledge-base', 'public');
            $validated['featured_image'] = $path;
        }

        $validated['is_published'] = $request->has('is_published');
        $validated['is_featured'] = $request->has('is_featured');

        // Set published_at if publishing for the first time
        if ($validated['is_published'] && empty($article->published_at) && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $article->update($validated);

        return redirect()
            ->route('super-admin.support.kb.index')
            ->with('success', 'Knowledge base article updated successfully!');
    }

    /**
     * Remove the specified article from storage.
     */
    public function destroy(KnowledgeBaseArticle $article)
    {
        // Delete featured image if exists
        if ($article->featured_image) {
            Storage::disk('public')->delete($article->featured_image);
        }

        $article->delete();

        return redirect()
            ->route('super-admin.support.kb.index')
            ->with('success', 'Knowledge base article deleted successfully!');
    }

    /**
     * Publish an article.
     */
    public function publish(KnowledgeBaseArticle $article)
    {
        $article->update([
            'is_published' => true,
            'published_at' => $article->published_at ?? now(),
        ]);

        return back()->with('success', 'Article published successfully!');
    }

    /**
     * Unpublish an article.
     */
    public function unpublish(KnowledgeBaseArticle $article)
    {
        $article->update(['is_published' => false]);

        return back()->with('success', 'Article unpublished successfully!');
    }
}
