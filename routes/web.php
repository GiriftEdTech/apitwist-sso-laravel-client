<?php

use Girift\SSO\Http\Controllers\SSOController;
use Illuminate\Support\Facades\Route;

Route::get('/sso/login', [SSOController::class, 'login'])->name('sso.login');
Route::get('/sso/callback', [SSOController::class, 'callback'])->name('sso.callback');
Route::get('/sso/logout', [SSOController::class, 'logout'])->name('sso.logout');
