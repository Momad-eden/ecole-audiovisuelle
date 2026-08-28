<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Gallery;
use App\Models\Partner;
use Illuminate\View\View;

class AboutController extends Controller
{
    /**
     * Affiche la page de présentation complète de l'école (L'école / À propos).
     */
    public function index(): View
    {
        $partners = Partner::where('is_active', true)
            ->latest()
            ->get();

        $coursesCount = Course::where('is_active', true)->count();
        $galleryHighlights = Gallery::where('is_active', true)->latest()->take(4)->get();

        return view('public.about', compact('partners', 'coursesCount', 'galleryHighlights'));
    }
}
