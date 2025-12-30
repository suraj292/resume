<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Get all published blogs with pagination and optional filters.
     */
    public function index(Request $request)
    {
        $query = Blog::with('category')
            ->published()
            ->latest();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        $perPage = $request->input('per_page', 12);
        $blogs = $query->paginate($perPage);

        return response()->json($blogs);
    }

    /**
     * Get a single blog by slug.
     */
    public function show($slug)
    {
        $blog = Blog::with('category')
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Increment view count
        $blog->incrementViews();

        return response()->json($blog);
    }

    /**
     * Get featured blog posts.
     */
    public function featured()
    {
        $blogs = Blog::with('category')
            ->published()
            ->featured()
            ->latest()
            ->limit(3)
            ->get();

        return response()->json($blogs);
    }

    /**
     * Get trending blog posts (by views).
     */
    public function trending()
    {
        $blogs = Blog::with('category')
            ->published()
            ->trending()
            ->limit(5)
            ->get();

        return response()->json($blogs);
    }

    /**
     * Get all active blog categories with post counts.
     */
    public function categories()
    {
        $categories = BlogCategory::active()
            ->withCount('blogs')
            ->ordered()
            ->get();

        return response()->json($categories);
    }

    /**
     * Get blogs by category slug.
     */
    public function byCategory($categorySlug)
    {
        $category = BlogCategory::where('slug', $categorySlug)
            ->active()
            ->firstOrFail();

        $blogs = Blog::with('category')
            ->where('category_id', $category->id)
            ->published()
            ->latest()
            ->paginate(12);

        return response()->json([
            'category' => $category,
            'blogs' => $blogs,
        ]);
    }
}
