<?php

use App\Http\Controllers\Upload\WordController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::get('/upload-word', [WordController::class, 'create'])->name('doc.create');
    Route::post('/upload-word', [WordController::class, 'store'])->name('doc.store');
    Route::get('/api/user/words', [WordController::class, 'index'])->name('doc.index');
    Route::get('/upload-word/{file}', [WordController::class, 'show'])->name('doc.show');
    Route::post('/upload-word/delete/{file}', [WordController::class, 'destroy'])->name('doc.destroy');
});
