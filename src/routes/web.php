<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/people', function () {
    return Inertia::render('People/Index');
})->middleware(['auth', 'verified'])->name('people.index');

Route::get('/roles', function () {
    return Inertia::render('Roles/Index');
})->middleware(['auth', 'verified'])->name('roles.index');

Route::get('/skills', function () {
    return Inertia::render('Skills/Index');
})->middleware(['auth', 'verified'])->name('skills.index');

require __DIR__ . '/settings.php';
