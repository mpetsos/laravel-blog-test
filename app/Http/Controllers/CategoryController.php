<?php

/*
 * Laravel Blog Test
 * by Thomas
 */

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        if (!auth()->user()->isAdmin())
            abort(403);

        $parents = Category::all();  // or only top-level categories
        return view('categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin())
            abort(403, 'Unauthorized');

        $request->validate(['name' => 'required|string|max:255']);
        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),  // generate slug automatically
        ]);

        return redirect()->route('categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        if (!auth()->user()->isAdmin())
            abort(403);

        $parents = Category::where('id', '!=', $category->id)->get();
        return view('categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        if (!auth()->user()->isAdmin())
            abort(403, 'Unauthorized');

        $request->validate(['name' => 'required|string|max:255']);
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return redirect()->route('categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        if (!auth()->user()->isAdmin())
            abort(403, 'Unauthorized');

        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted.');
    }
}
