<?php
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ImageController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/

// Auth routes
Auth::routes(['verify' => true, 'register' => false]);
Route::get('/logout', 'Auth\LoginController@logout');

// Frontend
Route::get('/', [PageController::class, 'index']);

// Url based images
Route::get('/img/{template}/{filename}/{maxW?}/{maxH?}/{coords?}', [ImageController::class, 'getResponse']);

/*
|--------------------------------------------------------------------------
| Admin routes
|--------------------------------------------------------------------------
|
*/

Route::middleware('auth:sanctum', 'verified')->group(function() {
  
  // Catch all routes
  Route::get('administration/{any?}', function () {
    return view('layout.cms');
  })->where('any', '.*')->middleware('role:admin')->name('cms');


});


