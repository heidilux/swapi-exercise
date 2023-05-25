<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FilmsController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\PlanetsController;

Route::get('/', static function () {
    return response()->json(['message' => 'success', 'ts' => now()]);
});

Route::prefix('people')->group(function () {
    Route::get('/', [PeopleController::class, 'all']);
    Route::get('{person}/{relation?}', [PeopleController::class, 'single']);
});

Route::prefix('films')->group(function () {
    Route::get('/', [FilmsController::class, 'all']);
    Route::get('{film}/{relation?}', [FilmsController::class, 'single']);
});

Route::prefix('planets')->group(function () {
    Route::get('/', [PlanetsController::class, 'all']);
    Route::get('population', [PlanetsController::class, 'totalPopulation']);
    Route::get('{planet}/{relation?}', [PlanetsController::class, 'single']);
});
