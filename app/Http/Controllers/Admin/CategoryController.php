<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminCategoryCreateRequest;
use App\Http\Requests\AdminCategoryUpdateRequest;
use App\Models\Category;
use App\Models\Language;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $languages = Language::all();
        $categories = Category::orderByDesc('created_at')->get();

        return view('admin.category.index', compact('languages', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::all();
        return view('admin.category.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminCategoryCreateRequest $request)
    {
        $validated = $request->validated();

        // Generate slug from category name
        $validated['slug'] = Str::slug($validated['name']);

        Category::create($validated);

        return redirect()->route('category.index')
            ->with('success', __('messages.category_created_successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $languages = Language::all();
        return view('admin.category.edit', compact('category', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminCategoryUpdateRequest $request, Category $category)
    {
        $validated = $request->validated();

        // Generate slug from category name
        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        return redirect()->route('category.index')
            ->with('success', __('messages.category_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => __('messages.category_deleted_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.something_went_wrong'),
            ], 500);
        }
    }
}
