<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Protected API Controllers
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CompanyProjectController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\Settings\ProjectStateController;
use App\Http\Controllers\Api\Settings\GenderController;
use App\Http\Controllers\Api\Settings\LanguageController;
use App\Http\Controllers\Api\Settings\RoleController;

// Public API Controllers
use App\Http\Controllers\Api\v1\CompanyController as CompanyApiController;
use App\Http\Controllers\Api\v1\UserController as UserControllerApiController;
//use App\Http\Controllers\Api\v1\ProjectController as ProjectApiController;

Route::middleware('auth:api')->group(function() {
  Route::get('v1/companies', [CompanyApiController::class, 'get']);
  Route::get('v1/company/{company}', [CompanyApiController::class, 'find']);
  Route::put('v1/company/{company}', [CompanyApiController::class, 'update']);
  Route::post('v1/company', [CompanyApiController::class, 'store']);
  Route::delete('v1/company/{company}', [CompanyApiController::class, 'destroy']);

  Route::get('v1/users', [UserControllerApiController::class, 'get']);
  Route::get('v1/user/{user}', [UserControllerApiController::class, 'find']);
  Route::post('v1/user', [UserControllerApiController::class, 'store']);
  Route::post('v1/user/register', [UserControllerApiController::class, 'register']);
  Route::put('v1/user/{user:uuid}', [UserControllerApiController::class, 'update']);
  Route::get('v1/user/state/{user:uuid}', [UserControllerApiController::class, 'toggle']);
  Route::delete('v1/user/{user:uuid}', [UserControllerApiController::class, 'destroy']);

  //Route::get('/v1/projects', [ProjectApiController::class, 'get']);
});

Route::middleware('auth:sanctum')->get('/user/authenticated', function (Request $request) {
  return $request->user();
});

Route::middleware('auth:sanctum')->group(function() {

  // Uploads
  Route::post('upload', [UploadController::class, 'store']);
  Route::delete('upload/{filename}', [UploadController::class, 'destroy']);

  // Users
  Route::get('users/staff', [UserController::class, 'getStaff']);
  Route::get('users/{company:uuid}', [UserController::class, 'get']);
  Route::get('user/authenticated', [UserController::class, 'getAuthenticated']);
  Route::get('user/{user:uuid}', [UserController::class, 'find']);
  Route::post('user', [UserController::class, 'store']);
  Route::post('user/register', [UserController::class, 'register']);
  Route::put('user/{user:uuid}', [UserController::class, 'update']);
  Route::get('user/state/{user:uuid}', [UserController::class, 'toggle']);
  Route::delete('user/{user:uuid}', [UserController::class, 'destroy']);

  // Projects
  Route::get('projects', [ProjectController::class, 'get']);
  Route::get('project/{project:uuid}', [ProjectController::class, 'find']);
  Route::post('project', [ProjectController::class, 'store']);
  Route::put('project/{project:uuid}', [ProjectController::class, 'update']);
  Route::get('project/state/{project:uuid}', [ProjectController::class, 'toggle']);
  Route::delete('project/{project:uuid}', [ProjectController::class, 'destroy']);

  // Project states
  Route::get('project-states', [ProjectStateController::class, 'get']);


  // Project companies
  Route::get('project-companies/{project}', [CompanyProjectController::class, 'findByProject']);
  Route::delete('project-company/{companyProject}', [CompanyProjectController::class, 'destroy']);

  // Companies
  Route::get('companies', [CompanyController::class, 'get']);
  Route::get('companies/clients', [CompanyController::class, 'getClients']);
  Route::get('company/owner', [CompanyController::class, 'getOwner']);
  Route::get('company/{company:uuid}', [CompanyController::class, 'find']);
  Route::post('company', [CompanyController::class, 'store']);
  Route::put('company/{company:uuid}', [CompanyController::class, 'update']);
  Route::get('company/state/{company:uuid}', [CompanyController::class, 'toggle']);
  Route::delete('company/{company:uuid}', [CompanyController::class, 'destroy']);

  // Genders
  Route::get('genders', [GenderController::class, 'get']);
  Route::get('gender/{gender}', [GenderController::class, 'find']);

  // Languages
  Route::get('languages', [LanguageController::class, 'get']);
  Route::get('language/{language}', [LanguageController::class, 'find']);

  // Roles
  Route::get('roles', [RoleController::class, 'get']);
  Route::get('role/{role}', [RoleController::class, 'find']);

  // Companies
  Route::get('messages/{project:uuid}', [MessageController::class, 'get']);
  Route::get('message/{message:uuid}', [MessageController::class, 'find']);
  Route::post('message/queue/{project:uuid}', [MessageController::class, 'store']);
  Route::delete('message/{message:uuid}', [MessageController::class, 'destroy']);

});



