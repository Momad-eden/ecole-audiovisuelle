<?php

use App\Http\Controllers\Admin\ApplicationDocumentController;
use App\Http\Controllers\Admin\CashExportController;
use App\Http\Controllers\Admin\CashReceiptController;
use Illuminate\Support\Facades\Route;

/*
| Le site public est servi par Next.js (dossier frontend/).
| Laravel expose l'administration (/admin, Filament) et l'API (/api).
*/

Route::get('/', fn () => redirect('/admin'));

Route::middleware('auth')->prefix('admin/fichiers')->name('admin.')->group(function () {
    Route::get('candidatures/{application}', ApplicationDocumentController::class)
        ->withTrashed()
        ->name('applications.document');

    Route::get('caisse/{transaction}/recu', CashReceiptController::class)
        ->name('cash.receipt');

    Route::get('caisse/export', CashExportController::class)->name('cash.export');
});
