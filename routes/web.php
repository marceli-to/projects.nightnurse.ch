<?php
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\MailgunWebhookController;

/*
|--------------------------------------------------------------------------
| Routes for my.nightnurse.ch
|--------------------------------------------------------------------------
|
*/

Route::get('/offerte/annehmen/{key}', [QuoteController::class, 'accept'])->name('quote.accept');
Route::get('/offerte/{slug?}/{key}', [QuoteController::class, 'index']);
Route::get('/offerte/{key}', [QuoteController::class, 'index']);

Route::post('/webhooks/mailgun', [MailgunWebhookController::class, 'handle'])
  ->middleware('verify.mailgun')
  ->name('webhooks.mailgun');

/*
|--------------------------------------------------------------------------
| Routes for projects.nightnurse.ch
|--------------------------------------------------------------------------
|
*/

// Auth routes
Auth::routes(['verify' => true, 'register' => false]);
Route::get('/logout', 'Auth\LoginController@logout');

// Frontend
Route::get('/', [PageController::class, 'index']);

// Set locale
Route::get('language/{locale}', function ($locale) {
  app()->setLocale($locale);
  session()->put('locale', $locale);
  return redirect()->back();
});

// Frontend register
Route::get('/register/complete', [RegisterController::class, 'complete'])->name('complete');
Route::get('/register/{user:uuid}', [RegisterController::class, 'index']);
Route::post('/register', [RegisterController::class, 'update'])->name('register');

// Frontend download
Route::get('/download/zip/{message:uuid}', [DownloadController::class, 'download']);

// Url based images
Route::get('/img/{template}/{filename}/{maxW?}/{maxH?}/{coords?}', [ImageController::class, 'getResponse']);

/*
|--------------------------------------------------------------------------
| Admin routes
|--------------------------------------------------------------------------
|
*/

Route::middleware('auth:sanctum', 'verified', 'project-redirect')->group(function() {
  Route::get('{any?}', function () {
    return view('layout.authenticated');
  })->where('any', '^((?!storage).)*$')->name('projects');
});


