<?php
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ImageController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/mailable', function () {
  $message = \App\Models\MessageUser::with('user', 'message.project', 'message.files', 'message.sender', 'message.users')->find(45);
  return new \App\Mail\Notification($message->message);
});


// Auth routes
Auth::routes(['verify' => true, 'register' => false]);
Route::get('/logout', 'Auth\LoginController@logout');

// Frontend
Route::get('/', [PageController::class, 'index']);

// Frontend register
Route::get('/register/complete', [RegisterController::class, 'complete'])->name('complete');
Route::get('/register/{user:uuid}', [RegisterController::class, 'index']);
Route::post('/register', [RegisterController::class, 'update'])->name('register');

// Url based images
Route::get('/img/{template}/{filename}/{maxW?}/{maxH?}/{coords?}', [ImageController::class, 'getResponse']);

/*
|--------------------------------------------------------------------------
| Admin routes
|--------------------------------------------------------------------------
|
*/

Route::middleware('auth:sanctum', 'verified')->group(function() {
  
  Route::get('{any?}', function () {
    return view('layout.authenticated');
  })->where('any', '^((?!storage).)*$')->middleware('role:admin')->name('projects');

});


