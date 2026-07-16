<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\News;

class HomeController extends Controller
{
    public function index()
    {
        $breakingNews = News::query()
            ->with('author')
            ->publiclyVisible()
            ->forLanguage(current_language())
            ->where('is_breaking_news', true)
            ->latest('id')
            ->limit(10)
            ->get();

        return view('frontend.home', compact('breakingNews'));
    }
}
