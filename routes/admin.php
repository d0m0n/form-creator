<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EntryController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\SlotController;
use Illuminate\Support\Facades\Route;

// 管理者ログイン（認証不要）
Route::get( '/admin/login',  [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login',  [AuthController::class, 'login'])    ->name('admin.login.post');
Route::post('/admin/logout', [AuthController::class, 'logout'])   ->name('admin.logout');

// 管理者認証必須
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // イベント管理
    Route::resource('events', EventController::class);

    // 試合枠管理（イベント配下）
    Route::post(  'events/{event}/slots',              [SlotController::class, 'store'])  ->name('slots.store');
    Route::get(   'events/{event}/slots/create',       [SlotController::class, 'create']) ->name('slots.create');
    Route::post(  'events/{event}/slots/bulk',         [SlotController::class, 'bulk'])   ->name('slots.bulk');
    Route::get(   'events/{event}/slots/{slot}/edit',  [SlotController::class, 'edit'])   ->name('slots.edit');
    Route::patch( 'events/{event}/slots/{slot}',       [SlotController::class, 'update']) ->name('slots.update');
    Route::delete('events/{event}/slots/{slot}',       [SlotController::class, 'destroy'])->name('slots.destroy');

    // 申込管理
    Route::get(   'events/{event}/entries',             [EntryController::class, 'index'])          ->name('entries.index');
    Route::get(   'events/{event}/participants',         [EntryController::class, 'participants'])   ->name('entries.participants');
    Route::get(   'events/{event}/entries/{entry}',     [EntryController::class, 'show'])           ->name('entries.show');
    Route::patch( 'events/{event}/entries/{entry}',     [EntryController::class, 'updateStatus'])   ->name('entries.updateStatus');

    // CSV出力
    Route::get('events/{event}/export',              [ExportController::class, 'entries'])      ->name('export.entries');
    Route::get('events/{event}/export/participants', [ExportController::class, 'participants']) ->name('export.participants');
});
