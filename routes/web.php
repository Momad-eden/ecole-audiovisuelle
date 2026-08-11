<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;

// Controllers Admin
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\AdmissionController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;

// Controller Public
use App\Http\Controllers\Public\AdmissionController as PublicAdmissionController;

// Models
use App\Models\Course;
use App\Models\Gallery;
use App\Models\News;


/*
|--------------------------------------------------------------------------
| SITE PUBLIC
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Accueil
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    $courses = Course::where('is_active', true)
        ->latest()
        ->get();

    $galleries = Gallery::where('is_active', true)
        ->latest()
        ->take(6)
        ->get();

    $news = News::where('is_published', true)
        ->where(function ($query) {

            $query
                ->whereNull('published_at')
                ->orWhere('published_at', '<=', now());
        })
        ->latest('published_at')
        ->take(3)
        ->get();

    return view(
        'public.home',
        compact(
            'courses',
            'galleries',
            'news'
        )
    );
})->name('public.home');


/*
|--------------------------------------------------------------------------
| Détail d'une formation
|--------------------------------------------------------------------------
*/

Route::get(
    '/formations/{course:slug}',
    function (Course $course) {

        abort_unless(
            $course->is_active,
            404
        );

        return view(
            'public.formation-show',
            compact('course')
        );
    }
)->name('public.courses.show');


/*
|--------------------------------------------------------------------------
| CANDIDATURES PUBLIQUES
|--------------------------------------------------------------------------
|
| Ces routes sont volontairement séparées des routes
| d'administration.
|
*/


/*
|--------------------------------------------------------------------------
| Formulaire public
|--------------------------------------------------------------------------
*/

Route::get(
    '/candidater',
    [
        PublicAdmissionController::class,
        'create'
    ]
)->name('public.admissions.create');


/*
|--------------------------------------------------------------------------
| Enregistrement d'une candidature publique
|--------------------------------------------------------------------------
*/

Route::post(
    '/candidater',
    [
        PublicAdmissionController::class,
        'store'
    ]
)->name('public.admissions.store');


/*
|--------------------------------------------------------------------------
| Succès candidature
|--------------------------------------------------------------------------
*/

Route::get(
    '/candidater/succes',
    [
        PublicAdmissionController::class,
        'success'
    ]
)->name('public.admissions.success');



/*
|--------------------------------------------------------------------------
| ADMINISTRATION
|--------------------------------------------------------------------------
|
| Toutes les routes ci-dessous nécessitent une authentification.
|
*/


Route::middleware(['auth'])->group(function () {


    /*
    |--------------------------------------------------------------------------
    | DIRECTEUR
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:directeur')->group(function () {

        /*
        | Gestion des utilisateurs
        */

        Route::resource(
            'users',
            UserController::class
        );
    });



    /*
    |--------------------------------------------------------------------------
    | DIRECTEUR + GESTIONNAIRE
    |--------------------------------------------------------------------------
    */

    Route::middleware(
        'role:directeur,gestionnaire'
    )->group(function () {


        /*
        |--------------------------------------------------------------------------
        | Formations
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'courses',
            CourseController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Étudiants
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'students',
            StudentController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Admissions
        |--------------------------------------------------------------------------
        |
        | Cette resource génère notamment :
        |
        | admissions.index
        | admissions.create
        | admissions.store
        | admissions.show
        | admissions.edit
        | admissions.update
        | admissions.destroy
        |
        */

        Route::resource(
            'admissions',
            AdmissionController::class
        );

        Route::post(
            '/admissions/{admission}/approve',
            [AdmissionController::class, 'approve']
        )->name('admissions.approve');

        Route::post(
            '/admissions/{admission}/reject',
            [AdmissionController::class, 'reject']
        )->name('admissions.reject');


        /*
        |--------------------------------------------------------------------------
        | Transformer une admission en étudiant
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/admissions/{admission}/enroll',
            [
                AdmissionController::class,
                'enroll'
            ]
        )->name('admissions.enroll');


        /*
        |--------------------------------------------------------------------------
        | Paiements
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'payments',
            PaymentController::class
        );
    });



    /*
    |--------------------------------------------------------------------------
    | DIRECTEUR + COMMUNICATION
    |--------------------------------------------------------------------------
    */

    Route::middleware(
        'role:directeur,communication'
    )->group(function () {


        /*
        |--------------------------------------------------------------------------
        | Galerie
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'galleries',
            GalleryController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Actualités
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'news',
            NewsController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Partenaires
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'partners',
            PartnerController::class
        );
    });



    /*
    |--------------------------------------------------------------------------
    | DIRECTEUR UNIQUEMENT
    |--------------------------------------------------------------------------
    */

    Route::middleware(
        'role:directeur'
    )->group(function () {


        /*
        |--------------------------------------------------------------------------
        | Paramètres
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/settings',
            [
                SettingController::class,
                'index'
            ]
        )->name('settings.index');


        Route::put(
            '/settings',
            [
                SettingController::class,
                'update'
            ]
        )->name('settings.update');
    });



    /*
    |--------------------------------------------------------------------------
    | TOUS LES UTILISATEURS CONNECTÉS
    |--------------------------------------------------------------------------
    */


    /*
    | Dashboard
    */

    Route::get(
        '/dashboard',
        [
            DashboardController::class,
            'index'
        ]
    )->name('dashboard');


    /*
    | Profil
    */

    Route::get(
        '/profile',
        [
            ProfileController::class,
            'edit'
        ]
    )->name('profile.edit');


    Route::patch(
        '/profile',
        [
            ProfileController::class,
            'update'
        ]
    )->name('profile.update');


    Route::delete(
        '/profile',
        [
            ProfileController::class,
            'destroy'
        ]
    )->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| AUTHENTIFICATION
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
