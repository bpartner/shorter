<?php

use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::domain('admin.' . config('app.base_domain'))
     ->group(function () {
         Route::get('/', function () {
             return redirect('dashboard');
         });
     });

Route::domain(config('app.base_domain'))
    ->group(function () {
        Route::get('/', function () {
            abort(404);
        });

        Route::get('{shortURLKey}', RedirectController::class);
    });

