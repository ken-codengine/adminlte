<?php

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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/admin/login', function () {
    return view('admin/auth/login');
})
    ->name('admin.login');
Route::get('/admin/home', function () {
    return view('admin/home');
})
    ->name('admin.home');

Route::post(
    '/admin/auth',
    '\App\Http\Controllers\Admin\LoginController@authenticate'
)->name('admin.auth');

Route::middleware(['auth:admin'])->group(function () {
    // ログイン認証が通ってない場合、URLで直接homeにアクセスしても画面が見れないように
    Route::get('/admin/home', function () {
        return view('/admin/home');
    })->name('admin.home');
});

Route::post(
    '/admin/logout',
    '\App\Http\Controllers\Admin\LoginController@logout'
)->name('admin.logout');
