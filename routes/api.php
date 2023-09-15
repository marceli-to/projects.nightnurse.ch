<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProjectQuoteController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CompanyProjectController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\MessageUserController;
use App\Http\Controllers\Api\TranslationController;
use App\Http\Controllers\Api\ReactionController;
use App\Http\Controllers\Api\ReactionTypeController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\Settings\ProjectStateController;
use App\Http\Controllers\Api\Settings\GenderController;
use App\Http\Controllers\Api\Settings\LanguageController;
use App\Http\Controllers\Api\Settings\RoleController;
use App\Http\Controllers\Api\v1\CompanyController as CompanyApiController;
use App\Http\Controllers\Api\v1\UserController as UserApiController;
use App\Http\Controllers\Api\v1\ProjectController as ProjectApiController;
use App\Http\Controllers\Api\v1\MessageController as MessageApiController;
use App\Http\Controllers\Api\v1\FeedbackController as FeedbackApiController;

// Public API
Route::middleware('auth:api')->group(function() {
  Route::get('v1/companies', [CompanyApiController::class, 'get']);
  Route::get('v1/company/{company}', [CompanyApiController::class, 'find']);
  Route::get('v1/company/{company}/users', [CompanyApiController::class, 'getUsersByCompany']);
  Route::put('v1/company/{company}', [CompanyApiController::class, 'update']);
  Route::post('v1/company', [CompanyApiController::class, 'store']);
  Route::delete('v1/company/{company}', [CompanyApiController::class, 'destroy']);

  Route::get('v1/users', [UserApiController::class, 'get']);
  Route::get('v1/user/{user}', [UserApiController::class, 'find']);
  Route::post('v1/user/register', [UserApiController::class, 'register']);
  Route::post('v1/user', [UserApiController::class, 'store']);
  Route::put('v1/user/{user}', [UserApiController::class, 'update']);
  Route::get('v1/user/state/{user}', [UserApiController::class, 'toggle']);
  Route::delete('v1/user/{user}', [UserApiController::class, 'destroy']);

  Route::get('v1/projects', [ProjectApiController::class, 'get']);
  Route::get('v1/project/{project}', [ProjectApiController::class, 'find']);
  Route::post('v1/project', [ProjectApiController::class, 'store']);
  Route::put('v1/project/{project}', [ProjectApiController::class, 'update']);
  Route::get('v1/project/state/{project}', [ProjectApiController::class, 'toggle']);
  Route::delete('v1/project/{project}', [ProjectApiController::class, 'destroy']);

  Route::post('v1/message', [MessageApiController::class, 'store']);
  Route::get('v1/messages/{project}', [MessageApiController::class, 'get']);

  Route::get('v1/feedbacks', [FeedbackApiController::class, 'get']);
  Route::get('v1/feedbacks/{project}', [FeedbackApiController::class, 'find']);
});

// Protected API routes
Route::middleware('auth:sanctum')->get('/user/authenticated', function (Request $request) {
  return $request->user();
});


Route::middleware('auth:sanctum')->group(function() {

  // User
  Route::get('user/authenticated', [UserController::class, 'getAuthenticated']);
  
  // Uploads
  Route::post('upload', [UploadController::class, 'store']);
  Route::delete('upload/{filename}', [UploadController::class, 'destroy']);

  // Projects
  Route::get('projects', [ProjectController::class, 'get']);
  Route::get('project/users/{project:uuid}', [ProjectController::class, 'getProjectUsers']);
  Route::get('project/{project:uuid}', [ProjectController::class, 'find']);

  // Messages
  Route::get('message/users/{message:uuid}', [MessageUserController::class, 'get']);

  // Messages
  Route::get('messages/{project:uuid}', [MessageController::class, 'get']);
  Route::get('message/{message:uuid}', [MessageController::class, 'find']);
  Route::post('message/queue/{project:uuid}', [MessageController::class, 'store']);
  Route::delete('message/{message:uuid}', [MessageController::class, 'destroy']);

  // Feedbacks
  Route::get('feedbacks/{project:uuid}', [FeedbackController::class, 'get']);
  Route::post('feedback', [FeedbackController::class, 'store']);

  // Profile
  Route::post('profile/switch-language', [ProfileController::class, 'switchLanguage']);
  Route::get('profile', [ProfileController::class, 'find']);
  Route::put('profile', [ProfileController::class, 'update']);

  // Genders
  Route::get('genders', [GenderController::class, 'get']);
  Route::get('gender/{gender}', [GenderController::class, 'find']);

  // Languages
  Route::get('languages', [LanguageController::class, 'get']);
  Route::get('language/{language}', [LanguageController::class, 'find']);

  // Translations
  Route::post('translate', [TranslationController::class, 'get']);

  // Reactions
  Route::get('reaction-types', [ReactionTypeController::class, 'get']);
  Route::post('reaction', [ReactionController::class, 'store']);

  Route::get('notification/latest', [NotificationController::class, 'findLatest']);

  // Routes for admins only
  Route::middleware('role:admin')->group(function() {

    // Projects
    Route::post('project/quote', [ProjectQuoteController::class, 'store']);
    Route::delete('project/quote/{projectQuote:uuid}', [ProjectQuoteController::class, 'destroy']);

    Route::get('projects-archive', [ProjectController::class, 'getArchive']);
    Route::get('project/companies/{project:uuid}', [ProjectController::class, 'getProjectCompanies']);
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

    // Users
    Route::get('users/staff', [UserController::class, 'getStaff']);
    Route::get('users/{company:uuid}', [UserController::class, 'get']);
    Route::get('user/{user:uuid}', [UserController::class, 'find']);
    Route::post('user', [UserController::class, 'store']);
    Route::post('user/register', [UserController::class, 'register']);
    Route::get('user/invite/{user:uuid}', [UserController::class, 'invite']);
    Route::put('user/{user:uuid}', [UserController::class, 'update']);
    Route::get('user/state/{user:uuid}', [UserController::class, 'toggle']);
    Route::delete('user/{user:uuid}', [UserController::class, 'destroy']);

    // Roles
    Route::get('roles', [RoleController::class, 'get']);
    Route::get('role/{role}', [RoleController::class, 'find']);

    // Notifications
    Route::get('notifications', [NotificationController::class, 'get']);
    Route::get('notification/{notification}', [NotificationController::class, 'find']);
    Route::post('notification', [NotificationController::class, 'store']);
    Route::put('notification/{notification}', [NotificationController::class, 'update']);
    Route::get('notification/state/{notification}', [NotificationController::class, 'toggle']);
    Route::delete('notification/{notification}', [NotificationController::class, 'destroy']);

  });

});