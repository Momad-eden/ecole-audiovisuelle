<?php

use Illuminate\Support\Facades\Route;

/*
| Le site public est servi par Next.js (dossier frontend/).
| Laravel expose l'administration (/admin, Filament) et l'API (/api).
*/

Route::get('/', fn () => redirect('/admin'));
