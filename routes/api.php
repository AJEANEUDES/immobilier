<?php

use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Superadmin\RoleController as SuperadminRoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
//ROUTE AUTH

// Auth::routes(['register' => true, 'verify' => false, 'reset' => false]);

//Route publiques

Route::group([

    'middleware' => 'api',
    "namespace" => "App\Http\Controllers",
    'prefix' => 'auth'

], function ($router) {

    Route::post('/inscription', [UserController::class, 'register']);
    Route::post('/connexion', [UserController::class, 'login']);

     //Reset Password

     Route::post('/reinitialiser-mot-de-passe', [PasswordResetController::class, 'resetPassword'])->name('reinitialiser.reset-code');
     Route::post('/reinitialiser-mot-de-passe/{reset_code}', [PasswordResetController::class, 'getPasswordReset'])->name('reinitialiser.password-reset');
     Route::get('/confirmation-email/verificationcode/{verification_code}', [UserController::class, 'verificationUser']);


//Route protégées

Route::post('/deconnexion', [UserController::class, 'logout']);
Route::get('/profil', [UserController::class, 'profile']);
Route::post('/rafraichir', [UserController::class, 'refresh']);
Route::post('/changer-mot-de-passe', [UserController::class, 'updateMotdepasse']);
Route::post('/mon-compte', [UserController::class, 'updateCompte']);


});




// ////////////////////////////////////////////////ROUTE LOGIN SUPERADMIN/////////////////////////////////////////////
// ////////////////////////////////////////////////ROUTE LOGIN SUPERADMIN/////////////////////////////////////////////
// ////////////////////////////////////////////////ROUTE LOGIN SUPERADMIN/////////////////////////////////////////////
// ////////////////////////////////////////////////ROUTE LOGIN SUPERADMIN/////////////////////////////////////////////
// ////////////////////////////////////////////////ROUTE LOGIN SUPERADMIN/////////////////////////////////////////////
// ////////////////////////////////////////////////ROUTE LOGIN SUPERADMIN/////////////////////////////////////////////
// ////////////////////////////////////////////////ROUTE LOGIN SUPERADMIN/////////////////////////////////////////////
// ////////////////////////////////////////////////ROUTE LOGIN SUPERADMIN/////////////////////////////////////////////
// ////////////////////////////////////////////////ROUTE LOGIN SUPERADMIN/////////////////////////////////////////////
// ////////////////////////////////////////////////ROUTE LOGIN SUPERADMIN/////////////////////////////////////////////
// ////////////////////////////////////////////////ROUTE LOGIN SUPERADMIN/////////////////////////////////////////////


Route::group([

    'prefix' => 'auth/superadmin',
        'middleware' => 'jwt',
        "namespace" => "App\Http\Controllers",
], function ($router) {

    //Role

    Route::get("/roles", [SuperadminRoleController::class, 'getAllRoles']);
    Route::post('/add-role', [SuperadminRoleController::class, 'storeRole']);
    Route::get("/get-one-role/{id}", [SuperadminRoleController::class, 'getOneRole']);
    Route::post('/update-role/{id}', [SuperadminRoleController::class, 'updateRole']);
    Route::delete('/delete-role/{id}', [SuperadminRoleController::class, 'deleteRole']);
});









// ////////////////////////////////////////////////ROUTE LOGIN ADMIN/////////////////////////////////////////////
// ////////////////////////////////////////////////ROUTE LOGIN ADMIN/////////////////////////////////////////////
// ////////////////////////////////////////////////ROUTE LOGIN ADMIN/////////////////////////////////////////////
// ////////////////////////////////////////////////ROUTE LOGIN ADMIN/////////////////////////////////////////////
// ////////////////////////////////////////////////ROUTE LOGIN ADMIN/////////////////////////////////////////////
// ////////////////////////////////////////////////ROUTE LOGIN ADMIN/////////////////////////////////////////////
// ////////////////////////////////////////////////ROUTE LOGIN ADMIN/////////////////////////////////////////////
// ////////////////////////////////////////////////ROUTE LOGIN ADMIN/////////////////////////////////////////////
// ////////////////////////////////////////////////ROUTE LOGIN ADMIN/////////////////////////////////////////////
// ////////////////////////////////////////////////ROUTE LOGIN ADMIN/////////////////////////////////////////////
// ////////////////////////////////////////////////ROUTE LOGIN ADMIN/////////////////////////////////////////////


Route::group([
    'prefix' => 'auth/Admin',
        'middleware' => 'jwt',
        "namespace" => "App\Http\Controllers",

], function () {

    //Role

    Route::get("/roles", [AdminRoleController::class, 'getAllRoles']);
    Route::post('/add-role', [AdminRoleController::class, 'storeRole']);
    Route::get("/get-one-role/{id}", [AdminRoleController::class, 'getOneRole']);
    Route::post('/update-role/{id}', [AdminRoleController::class, 'updateRole']);
    Route::delete('/delete-role/{id}', [AdminRoleController::class, 'deleteRole']);
});




