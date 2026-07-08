<?php

use App\Http\Controllers\EntryController;
use App\Http\Controllers\GuestEmailVerificationController;
use App\Http\Controllers\GuestEventController;
use Illuminate\Support\Facades\Route;

Route::middleware('App\Http\Middleware\IdentifyGuestUser')->group(function () {

    // 申込フォーム（利用者向け）
    Route::get( '/entry/{event:slug}',          [EntryController::class, 'index'])  ->name('entry.index');
    Route::post('/entry/{event:slug}/confirm',  [EntryController::class, 'confirm'])->name('entry.confirm');
    Route::post('/entry/{event:slug}/submit',   [EntryController::class, 'submit']) ->name('entry.submit');
    Route::get( '/entry/{event:slug}/complete', [EntryController::class, 'complete'])->name('entry.complete');

    // 申込編集（トークンベース）
    Route::get( '/entry/{event:slug}/edit/{token}',   [EntryController::class, 'edit'])  ->name('entry.edit');
    Route::post('/entry/{event:slug}/edit/{token}',   [EntryController::class, 'update'])->name('entry.update');
    Route::post('/entry/{event:slug}/cancel/{token}', [EntryController::class, 'cancel'])->name('entry.cancel');

    // ゲストによるイベント管理
    Route::get( '/manage/create',              [GuestEventController::class, 'create'])     ->name('guest.event.create');
    Route::post('/manage/verify-email',        [GuestEventController::class, 'verifyEmail'])->name('guest.event.verifyEmail');
    Route::get( '/manage/verify/{token}',      [GuestEventController::class, 'confirm'])    ->name('guest.event.confirm');
    Route::post('/manage/store',               [GuestEventController::class, 'store'])      ->name('guest.event.store');
    Route::get( '/manage/{event:slug}',        [GuestEventController::class, 'show'])       ->name('guest.event.show');
    Route::get( '/manage/{event:slug}/edit',   [GuestEventController::class, 'edit'])       ->name('guest.event.edit');
    Route::patch('/manage/{event:slug}',       [GuestEventController::class, 'update'])     ->name('guest.event.update');
    Route::get( '/manage/{event:slug}/entries',[GuestEventController::class, 'entries'])    ->name('guest.event.entries');
    Route::get( '/manage/{event:slug}/export', [GuestEventController::class, 'export'])     ->name('guest.event.export');

    // メール認証
    Route::post('/guest/email/send',                   [GuestEmailVerificationController::class, 'send'])  ->name('guest.email.send');
    Route::get( '/guest/email/verify/{verifyToken}',   [GuestEmailVerificationController::class, 'verify'])->name('guest.email.verify');
});
