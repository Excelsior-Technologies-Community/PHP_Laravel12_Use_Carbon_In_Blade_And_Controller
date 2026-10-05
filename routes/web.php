<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\CarbonStudioController;

Route::get('/', function () {
    return redirect()->route('profiles.index');
});

Route::resource('profiles', UserProfileController::class);

Route::get(
    'profiles/{id}/calculations',
    [UserProfileController::class, 'showCalculations']
)->name('profiles.calculations');

// Carbon Studio & Date Analytics Routes
Route::get('/carbon-studio', [CarbonStudioController::class, 'index'])->name('carbon.studio');
Route::post('/carbon-studio/renew/{id}', [CarbonStudioController::class, 'renewSubscription'])->name('carbon.studio.renew');
Route::get('/carbon-studio/export', [CarbonStudioController::class, 'exportDataset'])->name('carbon.studio.export');

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