<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Models\News;
use App\Models\Language;
use App\Models\Category;
use App\Models\Admin;
use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $languages = Language::all();
        $news = News::with('category', 'author')->orderByDesc('created_at')->get();

        return view('admin.news.index', compact('languages', 'news'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::all();
        $admins = Admin::all();

        return view('admin.news.create', compact('languages', 'admins'));
    }

    /**
     * Fetch categories by language (AJAX).
     */
    public function fetchCategory(Request $request)
    {
        $language = $request->query('lang');
        $categories = Category::where('language', $language)->get(['id', 'name']);

        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNewsRequest $request)
    {
        $validated = $request->validated();

        // Get current authenticated admin
        $validated['author_id'] = auth('admin')->id();

        // Generate slug
        $validated['slug'] = Str::slug($validated['title']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $this->uploadFile(
                $request->file('image'),
                'uploads/news'
            );
        }

        // Convert string "on" to boolean 1, or false if not present
        $validated['is_breaking_news'] = $request->has('is_breaking_news') ? 1 : 0;
        $validated['show_at_slider'] = $request->has('show_at_slider') ? 1 : 0;
        $validated['show_at_popular'] = $request->has('show_at_popular') ? 1 : 0;

        News::create($validated);

        return redirect()->route('news.index')
            ->with('success', __('messages.news_created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(News $news)
    {
        return view('admin.news.show', compact('news'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(News $news)
    {
        $languages = Language::all();
        $admins = Admin::all();

        return view('admin.news.edit', compact('news', 'languages', 'admins'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNewsRequest $request, News $news)
    {
        $validated = $request->validated();

        // Generate new slug if title changed
        $validated['slug'] = Str::slug($validated['title']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $this->uploadFile(
                $request->file('image'),
                'uploads/news',
                $news->image
            );
        }

        // Convert string "on" to boolean 1, or false if not present
        $validated['is_breaking_news'] = $request->has('is_breaking_news') ? 1 : 0;
        $validated['show_at_slider'] = $request->has('show_at_slider') ? 1 : 0;
        $validated['show_at_popular'] = $request->has('show_at_popular') ? 1 : 0;

        $news->update($validated);

        return redirect()->route('news.index')
            ->with('success', __('messages.news_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news)
    {
        try {
            // Delete image
            if ($news->image) {
                $path = public_path($news->image);
                if (file_exists($path)) {
                    unlink($path);
                }
            }

            $news->delete();

            return response()->json([
                'success' => true,
                'message' => __('messages.news_deleted_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.something_went_wrong'),
            ], 500);
        }
    }
}

