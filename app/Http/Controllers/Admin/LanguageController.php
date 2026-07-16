<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLanguageStoreRequest;
use App\Http\Requests\AdminLanguageToggleStatusRequest;
use App\Http\Requests\AdminLanguageUpdateRequest;
use App\Models\Language;
use Illuminate\Http\JsonResponse;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $languages = Language::orderByDesc('created_at')->get();

        return view('admin.language.index', compact('languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $availableLanguages = config('language');

        return view('admin.language.create', compact('availableLanguages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminLanguageStoreRequest $request)
    {
        $validated = $request->validated();

        // Add lang field from code if not already set
        if (! isset($validated['lang']) || empty($validated['lang'])) {
            $validated['lang'] = $validated['code'];
        }

        Language::create($validated);

        return redirect()->route('language.index')
            ->with('success', __('messages.language_created_successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Language $language)
    {
        $availableLanguages = config('language');

        return view('admin.language.edit', compact('language', 'availableLanguages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminLanguageUpdateRequest $request, Language $language)
    {
        $validated = $request->validated();

        // Add lang field from code if not already set
        if (! isset($validated['lang']) || empty($validated['lang'])) {
            $validated['lang'] = $validated['code'];
        }

        $language->update($validated);

        return redirect()->route('language.index')
            ->with('success', __('messages.language_updated_successfully'));
    }

    /**
     * Toggle supported language status fields via AJAX.
     */
    public function toggleStatusField(AdminLanguageToggleStatusRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $language = Language::findOrFail($validated['id']);

        $field = $validated['field'];
        $status = (bool) $validated['status'];

        $language->setAttribute($field, $status);
        $language->save();

        return response()->json([
            'status' => 'success',
            'message' => __('messages.updated_successfully'),
            'data' => [
                'id' => $language->id,
                'field' => $field,
                'value' => (bool) $language->getAttribute($field),
            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Language $language)
    {
        try {
            $language->delete();

            return response()->json([
                'success' => true,
                'message' => __('messages.language_deleted_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_deleting_language'),
            ], 500);
        }
    }
}
