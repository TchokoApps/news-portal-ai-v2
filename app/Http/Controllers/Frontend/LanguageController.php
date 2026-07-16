<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\FrontendLanguageChangeRequest;
use Illuminate\Http\JsonResponse;

class LanguageController extends Controller
{
    public function __invoke(FrontendLanguageChangeRequest $request): JsonResponse
    {
        $languageCode = $request->validated('language_code');

        set_language($languageCode);

        return response()->json([
            'status' => 'success',
            'language' => $languageCode,
        ]);
    }
}
