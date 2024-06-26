<?php

use Girift\SSO\Http\Controllers\AppController;
use Girift\SSO\Http\Controllers\InstitutionAuthorizationController;
use Girift\SSO\Http\Controllers\InstitutionCategoryController;
use Girift\SSO\Http\Controllers\InstitutionController;
use Girift\SSO\Http\Controllers\PublisherController;
use Girift\SSO\Http\Controllers\RoleUserController;
use Girift\SSO\Http\Controllers\SSOController;
use Girift\SSO\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/sso/login', [SSOController::class, 'login'])->name('sso.login');
Route::get('/sso/callback', [SSOController::class, 'callback'])->name('sso.callback');
Route::get('/sso/logout', [SSOController::class, 'logout'])->name('sso.logout');

$syncRoutes = [
    ['\App\Models\Institution', 'institutions', InstitutionController::class],
    ['\App\Models\Publisher', 'publishers', PublisherController::class],
    ['\App\Models\User', 'users', UserController::class],
    ['\App\Models\RoleUser', 'role_user', RoleUserController::class],
    ['\App\Models\InstitutionAuthorization', 'institution_authorization', InstitutionAuthorizationController::class],
    ['\App\Models\App', 'apps', AppController::class],
    ['\App\Models\InstitutionCategory', 'institution_categories', InstitutionCategoryController::class],
];

foreach ($syncRoutes as $syncRoute) {
    [$model, $route, $controller] = $syncRoute;
    if (class_exists($model)) {
        Route::apiResource('/sso/sync/'.$route, $controller)->except(['index', 'show']);
    }
}
