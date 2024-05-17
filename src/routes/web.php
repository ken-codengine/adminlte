<?php

use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Routing\Router;
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

Route::get('/admin/password/reset', [App\Http\Controllers\Admin\AdminResetPasswordController::class, 'showResetForm'])->name('admin.password.reset');

// Route::get('/admin/password/reset', function () {
//     return view('admin/auth/passwords/reset');
// })
//     ->name('admin.password.reset');

Route::get('/admin/home', function () {
    return view('admin/home');
})
    ->name('admin.home');

Route::post(
    '/admin/auth',
    '\App\Http\Controllers\Admin\LoginController@authenticate'
)->name('admin.auth');

// Route::middleware(['auth:admin'])->group(function () {
//     // ログイン認証が通ってない場合、URLで直接homeにアクセスしても画面が見れないように
//     Route::get('/admin/home', function () {
//         return view('/admin/home');
//     })->name('admin.home');

//     // ユーザー管理 ルーティング一覧(showは使わない)
//     Route::resource('admin/user', UsersController::class, ['except' => ['show']]);
// });

Route::group([
    'prefix'        => 'admin',
    'middleware'    => 'auth:admin',
], function (Router $router) {
    // ログイン認証が通ってない場合、URLで直接homeにアクセスしても画面が見れないように
    // $router->get('/home', function () {
    //     return view('/admin/home');
    // })->name('admin.home');
    // $router->get('/home', [ScheduleController::class, 'index'])->name('admin.home');
    // $router->resource('home', ScheduleController::class, ['except' => ['']]);
    $router->get('/home', [ScheduleController::class, 'index'])->name('admin.home');
    $router->post('/home/events', [ScheduleController::class, 'getEventsForPeriod']);
    $router->post('/home/store', [ScheduleController::class, 'store'])->name('admin.home.store');

    $router->resource('user', UsersController::class, ['except' => ['show']]);
});

Route::post(
    '/admin/logout',
    '\App\Http\Controllers\Admin\LoginController@logout'
)->name('admin.logout');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
