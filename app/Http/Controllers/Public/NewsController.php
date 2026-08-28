<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsController extends Controller
{
    /**
     * Affiche la liste des actualités publiées.
     */
    public function index(Request $request): View
    {
        $query = News::where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $news = $query->latest('published_at')->paginate(9)->withQueryString();

        $featuredNews = null;
        if (!$request->filled('search') && $news->currentPage() === 1 && $news->count() > 0) {
            $featuredNews = $news->first();
        }

        return view('public.news.index', compact('news', 'featuredNews'));
    }

    /**
     * Affiche un article d'actualité en détail.
     */
    public function show(News $news): View
    {
        abort_unless($news->is_published && ($news->published_at === null || $news->published_at <= now()), 404);

        $recentNews = News::where('is_published', true)
            ->where('id', '!=', $news->id)
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('public.news.show', compact('news', 'recentNews'));
    }
}
