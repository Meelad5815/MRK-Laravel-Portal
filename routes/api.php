<?php

use App\Http\Controllers\VuStudyController;
use Illuminate\Support\Facades\Route;

// Personal token authentication is handled by the controller; no browser session or CSRF cookie is needed.
Route::post('/vu-study/sync', [VuStudyController::class, 'syncCompanion'])
    ->middleware('throttle:20,1')
    ->name('api.vu-study.sync');
