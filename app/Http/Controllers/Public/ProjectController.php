<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Partner;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Affiche la page officielle du Projet EMSI & Grand Théâtre.
     */
    public function index(): View
    {
        $courses = Course::where('is_active', true)
            ->latest()
            ->get();

        $partners = Partner::where('is_active', true)
            ->latest()
            ->get();

        return view('public.project', compact('courses', 'partners'));
    }
}
