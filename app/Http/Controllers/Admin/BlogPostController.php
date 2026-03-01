<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blogPosts = BlogPost::with('user')
            ->latest()
            ->paginate(10);
        
        return Inertia::render('Admin/BlogPosts/Index', [
            'blogPosts' => $blogPosts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/BlogPosts/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|string|max:255',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['user_id'] = auth()->id();

        if ($validated['is_published'] && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        BlogPost::create($validated);

        return redirect()->route('blog-posts.index')
            ->with('success', 'Blog post created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(BlogPost $blogPost)
    {
        $blogPost->load('user');
        
        return Inertia::render('Admin/BlogPosts/Show', [
            'blogPost' => $blogPost,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlogPost $blogPost)
    {
        return Inertia::render('Admin/BlogPosts/Edit', [
            'blogPost' => $blogPost,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlogPost $blogPost)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|string|max:255',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        if ($blogPost->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if ($validated['is_published'] && empty($validated['published_at']) && !$blogPost->is_published) {
            $validated['published_at'] = now();
        }

        $blogPost->update($validated);

        return redirect()->route('blog-posts.index')
            ->with('success', 'Blog post updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogPost $blogPost)
    {
        $blogPost->delete();

        return redirect()->route('blog-posts.index')
            ->with('success', 'Blog post deleted successfully');
    }
}
