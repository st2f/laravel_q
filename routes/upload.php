<?php

use App\Http\Controllers\Upload\ImageController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {

    Route::get('upload', [ImageController::class, 'add'])->name('image.add');
    Route::post('upload', [ImageController::class, 'store'])->name('image.store');
    Route::post('upload/delete/{id}', [ImageController::class, 'destroy'])->name('image.destroy');

    Route::get('upload/list', function () {
        return Inertia::render('upload/List');
    })->name('image.list');
});
