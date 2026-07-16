<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminNewsToggleStatusRequest;
use App\Http\Requests\AdminNewsUpdateRequest;
use App\Http\Requests\StoreNewsRequest;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Language;
use App\Models\News;
use App\Models\Tag;
use App\Traits\FileUploadTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $newsByLanguage = News::query()
            ->with('category', 'author')
            ->latest('id')
            ->get()
            ->groupBy('language');

        return view('admin.news.index', compact('languages', 'newsByLanguage'));
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

        $news = News::create($validated);

        $this->syncNewsTags($news, (string) $request->input('tags', ''));

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
        $news->load(['category', 'tags']);

        $languages = Language::all();
        $categories = Category::query()
            ->where('language', old('language', $news->language))
            ->get();

        return view('admin.news.edit', compact('news', 'languages', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminNewsUpdateRequest $request, News $news)
    {
        $oldImagePath = $news->image;
        $oldImageIsShared = ! empty($oldImagePath)
            && News::query()
                ->where('image', $oldImagePath)
                ->whereKeyNot($news->getKey())
                ->exists();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadFile(
                $request->file('image'),
                'uploads/news'
            );
        }

        try {
            DB::transaction(function () use ($request, $news, $imagePath): void {
                $news->language = $request->string('language')->toString();
                $news->category_id = (int) $request->input('category_id');

                if (! empty($imagePath)) {
                    $news->image = $imagePath;
                }

                $news->title = $request->string('title')->toString();
                $news->slug = Str::slug($request->string('title')->toString());
                $news->content = $request->string('content')->toString();
                $news->meta_title = $request->input('meta_title');
                $news->meta_description = $request->input('meta_description');
                $news->is_breaking_news = $request->boolean('is_breaking_news');
                $news->show_at_slider = $request->boolean('show_at_slider');
                $news->show_at_popular = $request->boolean('show_at_popular');
                $news->status = $request->input('status', 'draft');

                $news->save();

                $this->syncNewsTags($news, (string) $request->input('tags', ''));
            });
        } catch (\Throwable $exception) {
            // Cleanup newly uploaded file when DB update fails.
            if (! empty($imagePath)) {
                $this->deleteFile($imagePath);
            }

            throw $exception;
        }

        if (
            ! empty($imagePath)
            && ! empty($oldImagePath)
            && $oldImagePath !== $imagePath
            && ! $oldImageIsShared
        ) {
            $this->deleteFile($oldImagePath);
        }

        return redirect()->route('news.index')
            ->with('success', __('messages.news_updated_successfully'));
    }

    /**
     * Clone a news record for multilingual editing.
     */
    public function cloneNews(News $news)
    {
        $news->load('tags');

        $copy = DB::transaction(function () use ($news): News {
            $copy = $news->replicate();

            $copy->title = $news->title.' (Copy '.now()->format('YmdHis').')';
            $copy->slug = Str::slug($copy->title.'-'.Str::uuid());
            $copy->status = 'draft';
            $copy->is_approved = false;
            $copy->is_breaking_news = false;
            $copy->show_at_slider = false;
            $copy->show_at_popular = false;

            $copy->save();

            $copy->tags()->sync($news->tags->modelKeys());

            return $copy;
        });

        return redirect()->route('news.edit', $copy)
            ->with('success', __('messages.news_copied_successfully'));
    }

    /**
     * Sync comma-separated tags to news pivot.
     */
    private function syncNewsTags(News $news, string $rawTags): void
    {
        $tagNames = collect(explode(',', $rawTags))
            ->map(fn (string $tag): string => trim($tag))
            ->filter()
            ->unique(fn (string $tag): string => Str::lower($tag))
            ->values();

        $tagIds = $tagNames
            ->map(function (string $tagName): int {
                return Tag::firstOrCreate([
                    'name' => $tagName,
                ])->id;
            })
            ->all();

        $news->tags()->sync($tagIds);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news)
    {
        $imagePath = $news->image;
        $imageIsShared = ! empty($imagePath)
            && News::query()
                ->where('image', $imagePath)
                ->whereKeyNot($news->getKey())
                ->exists();

        try {
            $news->delete();

            if (! empty($imagePath) && ! $imageIsShared) {
                $this->deleteFile($imagePath);
            }

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

    /**
     * Toggle breaking news status via AJAX.
     */
    public function toggleBreakingNews(Request $request, News $news)
    {
        try {
            $news->is_breaking_news = ! $news->is_breaking_news;
            $news->save();

            return response()->json([
                'success' => true,
                'is_breaking_news' => $news->is_breaking_news,
                'message' => __('messages.updated_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.something_went_wrong'),
            ], 500);
        }
    }

    /**
     * Toggle slider status via AJAX.
     */
    public function toggleSlider(Request $request, News $news)
    {
        try {
            $news->show_at_slider = ! $news->show_at_slider;
            $news->save();

            return response()->json([
                'success' => true,
                'show_at_slider' => $news->show_at_slider,
                'message' => __('messages.updated_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.something_went_wrong'),
            ], 500);
        }
    }

    /**
     * Toggle popular status via AJAX.
     */
    public function togglePopular(Request $request, News $news)
    {
        try {
            $news->show_at_popular = ! $news->show_at_popular;
            $news->save();

            return response()->json([
                'success' => true,
                'show_at_popular' => $news->show_at_popular,
                'message' => __('messages.updated_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.something_went_wrong'),
            ], 500);
        }
    }

    /**
     * Toggle publication status via AJAX.
     */
    public function toggleStatus(Request $request, News $news)
    {
        try {
            $newStatus = $news->status === 'published' ? 'draft' : 'published';
            $news->status = $newStatus;
            $news->save();

            return response()->json([
                'success' => true,
                'status' => $news->status,
                'message' => __('messages.updated_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.something_went_wrong'),
            ], 500);
        }
    }

    /**
     * Toggle any supported status field via PATCH request.
     *
     * Supports toggling: is_breaking_news, show_at_slider, show_at_popular, status
     */
    public function toggleStatusField(AdminNewsToggleStatusRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $news = News::findOrFail($validated['id']);

        $field = $validated['field'];
        $status = (bool) $validated['status'];

        // Handle the status field (ENUM: draft/published)
        if ($field === 'status') {
            $news->setAttribute('status', $status ? 'published' : 'draft');
            $savedValue = $news->status;
        } else {
            // Handle boolean fields
            $news->setAttribute($field, $status);
            $savedValue = (bool) $news->getAttribute($field);
        }

        $news->save();

        return response()->json([
            'status' => 'success',
            'message' => __('messages.updated_successfully'),
            'data' => [
                'id' => $news->id,
                'field' => $field,
                'value' => $field === 'status' ? ($savedValue === 'published') : $savedValue,
            ],
        ]);
    }
}
