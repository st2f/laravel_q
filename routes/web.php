<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

//Route::get('benchmark', function() {
//    \Illuminate\Support\Benchmark::dd([
//        fn () => WordProcessor::dispatch(),
//        fn () => WordProcessor::dispatch(),
//    ]);
//    // res
//    //    [
//    //        2330,
//    //        5211
//    //    ]
//});

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/upload.php';
require __DIR__.'/uploadWord.php';
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
