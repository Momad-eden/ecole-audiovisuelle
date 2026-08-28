<?php

namespace App\View\Composers;

use App\Models\Course;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SettingComposer
{
    /**
     * Lie les données de paramètres et de formations à la vue.
     */
    public function compose(View $view): void
    {
        $siteSettings = Setting::first();

        $headerCourses = collect();
        try {
            if (Schema::hasTable('courses')) {
                $headerCourses = Course::where('is_active', true)
                    ->select('id', 'title', 'slug', 'category', 'level', 'duration', 'image')
                    ->orderBy('title')
                    ->take(8)
                    ->get();
            }
        } catch (\Throwable $e) {
            $headerCourses = collect();
        }

        $view->with([
            'siteSettings' => $siteSettings,
            'headerCourses' => $headerCourses,
        ]);
    }
}
