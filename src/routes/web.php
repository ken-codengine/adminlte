<?php

use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\LockMonthController;
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
// 管理者ログイン関連
Route::get('/', function () {
    return view('auth/login');
});
Route::get('/admin/login', function () {
    return view('admin/auth/login');
})
    ->name('admin.login');
Route::post(
    '/admin/logout',
    '\App\Http\Controllers\Admin\LoginController@logout'
)->name('admin.logout');
Route::get('/admin/password/reset', [App\Http\Controllers\Admin\AdminResetPasswordController::class, 'showResetForm'])->name('admin.password.reset');
// Route::get('/admin/password/reset', function () {
//     return view('admin/auth/passwords/reset');
// })
//     ->name('admin.password.reset');
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


// 管理画面関連
Route::get('/admin/home', function () {
    return view('admin/home');
})
    ->name('admin.home');


//管理画面へはScheduleControllerでユーザー全員のshiftを表示する
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
    $router->post('/home/events', [ScheduleController::class, 'show'])->name('admin.home.events');
    $router->post('/home/store', [ScheduleController::class, 'store'])->name('admin.home.store');
    $router->post('/home/destroy', [ScheduleController::class, 'destroy'])->name('admin.home.destroy');

    //管理者がユーザーを管理するためのルーティング
    $router->resource('user', UsersController::class, ['except' => ['show']]);

    // カレンダーロック機能のルーティング（管理画面側で登録・削除）
    $router->get('/home/lock_month', [LockMonthController::class, 'index'])->name('admin.home.lock_month');
    $router->post('/home/lock_month/store', [LockMonthController::class, 'store'])->name('admin.home.lock_month.store');
    $router->delete('/home/lock_month/delete', [LockMonthController::class, 'delete'])->name('admin.home.lock_month.delete');
});

// カレンダーロック機能のルーティング（ユーザー画面側呼び出し）
Route::post('/home/lock_month/show', [App\Http\Controllers\LockMonthController::class, 'show'])->name('home.lock_month.show');

//Laravel/uiデフォルトのログイン機能を使用
Auth::routes();

//ユーザ画面ではShiftControllerで自分のshiftだけを表示する
Route::get('/home', [App\Http\Controllers\ShiftController::class, 'index'])->name('home');
Route::post('/home/show', [App\Http\Controllers\ShiftController::class, 'show'])->name('home.show');
Route::post('/home/store', [App\Http\Controllers\ShiftController::class, 'store'])->name('home.store');
Route::post('/home/delete', [App\Http\Controllers\ShiftController::class, 'destroy'])->name('home.delete');
