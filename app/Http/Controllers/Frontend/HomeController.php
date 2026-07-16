<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
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

    public function showNews(string $slug): View
    {
        $news = News::query()
            ->with([
                'author',
                'category',
                'tags',
            ])
            ->publiclyVisible()
            ->forLanguage(current_language())
            ->where('slug', $slug)
            ->firstOrFail();

        $this->recordNewsView($news);

        return view('frontend.news.details', compact('news'));
    }

    private function recordNewsView(News $news): void
    {
        $viewedNewsIds = array_map(
            'intval',
            (array) session()->get('viewed_news', [])
        );

        if (in_array($news->getKey(), $viewedNewsIds, true)) {
            return;
        }

        $news->increment('views', 1, []);

        $viewedNewsIds[] = (int) $news->getKey();

        session()->put(
            'viewed_news',
            array_values(array_unique($viewedNewsIds))
        );

        $news->refresh();
    }
}
