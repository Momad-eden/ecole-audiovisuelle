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

// Controllers Public
use App\Http\Controllers\Public\HomeController as PublicHomeController;
use App\Http\Controllers\Public\AboutController as PublicAboutController;
use App\Http\Controllers\Public\CourseController as PublicCourseController;
use App\Http\Controllers\Public\GalleryController as PublicGalleryController;
use App\Http\Controllers\Public\NewsController as PublicNewsController;
use App\Http\Controllers\Public\AdmissionController as PublicAdmissionController;
use App\Http\Controllers\Public\ProjectController as PublicProjectController;
use App\Http\Controllers\Public\VaeController as PublicVaeController;


/*
|--------------------------------------------------------------------------
| SITE PUBLIC
|--------------------------------------------------------------------------
*/

// Accueil
Route::get('/', PublicHomeController::class)->name('public.home');

// Le Projet (EMSI & Grand Théâtre)
Route::get('/projet', [PublicProjectController::class, 'index'])->name('public.project');

// Dispositif VAE
Route::get('/vae', [PublicVaeController::class, 'index'])->name('public.vae');

// L'école (À propos)
Route::get('/ecole', [PublicAboutController::class, 'index'])->name('public.about');

// Catalogue des Formations & Fiche détaillée
Route::get('/formations', [PublicCourseController::class, 'index'])->name('public.courses.index');
Route::get('/formations/{course:slug}', [PublicCourseController::class, 'show'])->name('public.courses.show');

// Galerie Médias
Route::get('/galerie', [PublicGalleryController::class, 'index'])->name('public.gallery.index');

// Journal & Actualités
Route::get('/actualites', [PublicNewsController::class, 'index'])->name('public.news.index');
Route::get('/actualites/{news:slug}', [PublicNewsController::class, 'show'])->name('public.news.show');


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
    '/admission',
    [
        PublicAdmissionController::class,
        'create'
    ]
)->name('public.admissions.create');

Route::redirect('/candidater', '/admission', 301);


/*
|--------------------------------------------------------------------------
| Enregistrement d'une candidature publique
|--------------------------------------------------------------------------
*/

Route::post(
    '/admission',
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
    '/admission/succes',
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
        | Caisse & Comptabilité (Paiements & Dépenses)
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/payments/{payment}/receipt',
            [PaymentController::class, 'receipt']
        )->name('payments.receipt');

        Route::get(
            '/comptabilite',
            [PaymentController::class, 'accounting']
        )->name('accounting.index');

        Route::get(
            '/comptabilite/export',
            [PaymentController::class, 'export']
        )->name('accounting.export');

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
