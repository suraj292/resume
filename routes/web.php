<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Home', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('/builder', function () {
    return Inertia::render('Builder');
})->name('builder');

Route::get('/ats-checker', function () {
    return Inertia::render('AtsChecker');
})->name('ats-checker');

Route::get('/templates', function () {
    return Inertia::render('Templates');
})->name('templates');

Route::get('/pricing', function () {
    return Inertia::render('Pricing');
})->name('pricing');

Route::get('/auth', function () {
    return Inertia::render('Auth');
})->name('auth');

Route::get('/contact', function () {
    return Inertia::render('Contact');
})->name('contact');

Route::get('/blog', function () {
    return Inertia::render('Blog');
})->name('blog');

Route::get('/blog/{id}', function ($id) {
    return Inertia::render('BlogDetail', ['id' => $id]);
})->name('blog.detail');

require __DIR__.'/settings.php';
