<?php

use App\Http\Controllers\DefaultController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\RunnerController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Authorization;
use App\Http\Middleware\EnsureUserIsAdmin;


Route::middleware(Authorization::class)->prefix('v1')->group(function() {
	Route::controller(DefaultController::class)->group(function () {
		Route::withoutMiddleware(Authorization::class)->post('/login', 'login');
		Route::withoutMiddleware(Authorization::class)->get('/stages', 'getStages');
	});

	Route::controller(TeamController::class)->group(function() {
		Route::get('/teams/{teamId}', 'getTeam');

		Route::put('/teams/{teamId}', 'updateTeam')->middleware(EnsureUserIsAdmin::class);
		Route::delete('/teams/{teamId}', 'deleteTeam')->middleware(EnsureUserIsAdmin::class);
	});

	Route::controller(RunnerController::class)->group(function() {
		Route::get('/teams/{teamId}/runners', 'getRunners');
		Route::get('/teams/{teamId}/runners/{runnerId}', 'getRunner');

		Route::post('/teams/{teamId}/runners', 'createRunner')->middleware(EnsureUserIsAdmin::class);
		Route::put('/teams/{teamId}/runners/{runnerId}', 'updateRunner')->middleware(EnsureUserIsAdmin::class);
		Route::delete('/teams/{teamId}/runners/{runnerId}', 'deleteRunner')->middleware(EnsureUserIsAdmin::class);
	});
});