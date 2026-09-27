<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1/public')->middleware('throttle:public-api')->group(function () {
    //
});
