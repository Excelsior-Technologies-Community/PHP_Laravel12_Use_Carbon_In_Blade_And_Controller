<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserProfileController;

Route::get('/', function () {
    return redirect()->route('profiles.index');
});

Route::resource('profiles', UserProfileController::class);

Route::get(
    'profiles/{id}/calculations',
    [UserProfileController::class, 'showCalculations']
)->name('profiles.calculations');

Route::get('/carbon-examples', function () {

    $now = now();
    $tomorrow = now()->addDay();
    $formattedDate = now()->format('F j, Y');
    $humanReadable = now()->diffForHumans();

    return view(
        'carbon-examples',
        compact(
            'now',
            'tomorrow',
            'formattedDate',
            'humanReadable'
        )
    );
});