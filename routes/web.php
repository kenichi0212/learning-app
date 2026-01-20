<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\LearningSessionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [LearningSessionController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    //目標一覧・登録画面
    Route::get('/goals', [GoalController::class, 'index'])->name('goals.index');
    //目標の保存
    Route::post('/goals', [GoalController::class, 'store'])->name('goals.store');
    //学習画面の表示
    Route::get('/learning', [LearningSessionController::class, 'show'])->name('learning.show');
    //sessionの開始
    Route::post('/learning/start', [LearningSessionController::class, 'start'])->name('learning.start');
    // 5分おきの更新通知（API的役割）
    Route::post('/learning/update/{id}', [LearningSessionController::class, 'update'])->name('learning.update');
    //sessionの終了
    Route::post('/learning/stop/{id}', [LearningSessionController::class, 'stop'])->name('learning.stop');
    //学習履歴一覧
    Route::get('/history', [LearningSessionController::class, 'index'])->name('learning-sessions.index');
    //logo
    Route::get('/post', [LearningSessionController::class, 'post'])->name('post.index');
});

require __DIR__ . '/auth.php';
