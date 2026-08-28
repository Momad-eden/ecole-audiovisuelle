<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\View\View;

class GalleryController extends Controller
{
    /**
     * Affiche la page complète de la galerie médias.
     */
    public function index(): View
    {
        $galleries = Gallery::where('is_active', true)
            ->latest()
            ->get();

        $photoCount = $galleries->where('type', 'image')->count();
        $videoCount = $galleries->where('type', 'video')->count();

        return view('public.gallery', compact('galleries', 'photoCount', 'videoCount'));
    }
}
