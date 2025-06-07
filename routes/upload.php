<?php

use App\Http\Controllers\Upload\ImageController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::get('/upload', [ImageController::class, 'create'])->name('image.create');
    Route::post('/upload', [ImageController::class, 'store'])->name('image.store');
    Route::get('/api/user/images', [ImageController::class, 'index'])->name('images.index');
    Route::get('/upload/{file}', [ImageController::class, 'show'])->name('image.show');
    Route::post('/upload/delete/{file}', [ImageController::class, 'destroy'])->name('image.destroy');
});
