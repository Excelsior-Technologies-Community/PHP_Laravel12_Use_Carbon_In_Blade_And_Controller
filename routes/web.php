<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserProfileController;

Route::get('/', function () {
    return view('welcome');
});

// User Profile Routes
Route::resource('profiles', UserProfileController::class);
Route::get('profiles/{id}/calculations', [UserProfileController::class, 'showCalculations'])
    ->name('profiles.calculations');

// Example routes for different Carbon demonstrations
Route::get('/carbon-examples', function () {
    // Quick examples in routes
    $now = now(); // now() is a Laravel helper that returns Carbon instance
    $tomorrow = now()->addDay();
    $formattedDate = now()->format('F j, Y');
    $humanReadable = now()->diffForHumans();
    
    return view('carbon-examples', compact(
        'now',
        'tomorrow',
        'formattedDate',
        'humanReadable'
    ));
});