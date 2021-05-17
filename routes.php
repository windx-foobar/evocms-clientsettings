<?php

use EvolutionCMS\ClientSettings\Controller;
use Illuminate\Support\Facades\Route;

Route::pattern('tab', '[a-z0-9_-]+');

Route::prefix('/{tab?}')->group(function() {
    Route::get('',  [Controller::class, 'show'])->name('cs::show');
    Route::post('', [Controller::class, 'save']);
});
