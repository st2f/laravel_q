<?php

use App\Http\Controllers\Upload\WordController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::get('/upload-word', [WordController::class, 'create'])->name('word.create');
    Route::post('/upload-word', [WordController::class, 'store'])->name('word.store');
    Route::get('/api/user/words', [WordController::class, 'index'])->name('word.index');
    Route::get('/upload-word/{file}', [WordController::class, 'show'])->name('word.show');
    Route::post('/upload-word/delete/{file}', [WordController::class, 'destroy'])->name('word.destroy');
});
