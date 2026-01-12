<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard/analytics', function () {
    return Inertia::render('Dashboard/Analytics');
})->middleware(['auth', 'verified'])->name('dashboard.analytics');

Route::get('/people', function () {
    return Inertia::render('People/Index');
})->middleware(['auth', 'verified'])->name('people.index');

Route::get('/people/{id}', function ($id) {
    return Inertia::render('People/Show', ['id' => $id]);
})->middleware(['auth', 'verified'])->name('people.show');

Route::get('/roles', function () {
    return Inertia::render('Roles/Index');
})->middleware(['auth', 'verified'])->name('roles.index');

Route::get('/roles/{id}', function ($id) {
    return Inertia::render('Roles/Show', ['id' => $id]);
})->middleware(['auth', 'verified'])->name('roles.show');

Route::get('/skills', function () {
    return Inertia::render('Skills/Index');
})->middleware(['auth', 'verified'])->name('skills.index');

Route::get('/gap-analysis', function () {
    return Inertia::render('GapAnalysis/Index');
})->middleware(['auth', 'verified'])->name('gap-analysis.index');

Route::get('/learning-paths', function () {
    return Inertia::render('LearningPaths/Index');
})->middleware(['auth', 'verified'])->name('learning-paths.index');

Route::get('/marketplace', function () {
    return Inertia::render('Marketplace/Index');
})->middleware(['auth', 'verified'])->name('marketplace.index');

// Alias routes for new frontend naming: /scenario-planning
Route::get('/scenario-planning', function () {
    return Inertia::render('ScenarioPlanning/ScenarioList');
})->middleware(['auth', 'verified'])->name('scenario-planning.index');

Route::get('/scenario-planning/{id}', function ($id) {
    return Inertia::render('ScenarioPlanning/ScenarioDetail', ['id' => $id]);
})->middleware(['auth', 'verified'])->name('scenario-planning.show');

require __DIR__ . '/settings.php';
