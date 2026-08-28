<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Gallery;
use App\Models\News;
use App\Models\Partner;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Affiche la page d'accueil du site public.
     */
    public function __invoke(): View
    {
        $courses = Course::where('is_active', true)
            ->latest()
            ->get();

        $galleries = Gallery::where('is_active', true)
            ->latest()
            ->take(12)
            ->get();

        $news = News::where('is_published', true)
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->latest('published_at')
            ->take(3)
            ->get();

        $partners = Partner::where('is_active', true)
            ->latest()
            ->get();

        return view('public.home', compact(
            'courses',
            'galleries',
            'news',
            'partners'
        ));
    }
}
