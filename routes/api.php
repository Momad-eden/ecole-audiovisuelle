<?php

use App\Http\Controllers\Api\Public\ContentController;
use App\Http\Controllers\Api\Public\FormController;
use App\Http\Controllers\Api\Public\ImpactLiveController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/public')->middleware('throttle:public-api')->group(function () {
    Route::controller(ContentController::class)->group(function () {
        Route::get('site', 'site');
        Route::get('pages/{slug}', 'page')->where('slug', '[a-z0-9/-]+');
        Route::get('preview', 'preview');
        Route::get('rooms', 'rooms');
        Route::get('rooms/{slug}', 'room');
        Route::get('artworks', 'artworks');
        Route::get('artworks/{slug}', 'artwork');
        Route::get('exhibitions', 'exhibitions');
        Route::get('exhibitions/{slug}', 'exhibition');
        Route::get('programs', 'programs');
        Route::get('programs/{slug}', 'program');
        Route::get('tracks', 'tracks');
        Route::get('offerings', 'offerings');
        Route::get('news', 'news');
        Route::get('news/{slug}', 'newsItem');
        Route::get('faqs', 'faqs');
        Route::get('partners', 'partners');
        Route::get('redirects', 'redirects');
        Route::get('sitemap', 'sitemap');
    });

    Route::controller(ImpactLiveController::class)->group(function () {
        Route::get('services', 'services');
        Route::get('equipment-categories', 'categories');
        Route::get('equipment', 'equipment');
        Route::get('equipment/{slug}', 'equipmentItem');
        Route::get('packs', 'packs');
        Route::get('agenda', 'agenda');
        Route::get('agenda/{slug}', 'agendaEvent');
        Route::get('places', 'places');
    });

    Route::post('booking-requests', [FormController::class, 'booking'])->middleware('throttle:bookings');
    Route::post('applications', [FormController::class, 'application'])->middleware('throttle:applications');
    Route::post('contact-messages', [FormController::class, 'contact'])->middleware('throttle:contact');
});
