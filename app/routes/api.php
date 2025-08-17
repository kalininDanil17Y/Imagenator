<?php

use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

Route::post('/upload', [UploadController::class, 'store']);
