<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\View\View;

class VaeController extends Controller
{
    /**
     * Affiche la page de présentation du dispositif VAE.
     */
    public function index(): View
    {
        $courses = Course::where('is_active', true)
            ->latest()
            ->get();

        return view('public.vae', compact('courses'));
    }
}
