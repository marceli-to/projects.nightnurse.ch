<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\CompanyController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});

Route::middleware('auth:sanctum')->group(function() {
  Route::get('user', [UserController::class, 'find']);

  // Uploads
  Route::post('image/upload', [UploadController::class, 'image']);
  Route::post('file/upload', [UploadController::class, 'file']);

  // Companies
  Route::get('companies', [CompanyController::class, 'get']);
  Route::get('company/{company}', [CompanyController::class, 'find']);
  Route::post('company', [CompanyController::class, 'store']);
  Route::put('company/{company}', [CompanyController::class, 'update']);
  Route::get('company/state/{company}', [CompanyController::class, 'toggle']);
  Route::delete('company/{company}', [CompanyController::class, 'destroy']);

});



