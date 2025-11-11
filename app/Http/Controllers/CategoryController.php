<?php

/*
 * Laravel Blog Test
 * by Thomas
 * Category Controller
 */

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Apply 'auth' middleware, except for index and show.
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display list of categories.
     */
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    /**
     * Show form to create a new category (Admin only).
     */
    public function create()
    {
        // Admin check
        if (!auth()->user()->isAdmin())
            abort(403);

        $parents = Category::all();
        return view('categories.create', compact('parents'));
    }

    /**
     * Store a new category (Admin only).
     */
    public function store(Request $request)
    {
        // Admin check
        if (!auth()->user()->isAdmin())
            abort(403, 'Unauthorized');

        // Validate input
        $request->validate(['name' => 'required|string|max:255']);

        // Create category and slug
        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('categories.index')->with('success', 'Category created.');
    }

    /**
     * Show form to edit a category (Admin only).
     */
    public function edit(Category $category)
    {
        // Admin check
        if (!auth()->user()->isAdmin())
            abort(403);

        // Get parents, excluding self
        $parents = Category::where('id', '!=', $category->id)->get();
        return view('categories.edit', compact('category', 'parents'));
    }

    /**
     * Update the category (Admin only).
     */
    public function update(Request $request, Category $category)
    {
        // Admin check
        if (!auth()->user()->isAdmin())
            abort(403, 'Unauthorized');

        // Validate input
        $request->validate(['name' => 'required|string|max:255']);

        // Update category and slug
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return redirect()->route('categories.index')->with('success', 'Category updated.');
    }

    /**
     * Delete the category (Admin only).
     */
    public function destroy(Category $category)
    {
        // Admin check
        if (!auth()->user()->isAdmin())
            abort(403, 'Unauthorized');

        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted.');
    }
}
