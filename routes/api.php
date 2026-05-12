<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TrackSessionController;

Route::post('/track-session', TrackSessionController::class);
