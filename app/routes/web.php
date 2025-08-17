<?php

use App\Http\Controllers\PreviewController;
use Illuminate\Support\Facades\Route;

Route::get('/{uuid}/-/preview/{w}/{format?}', [PreviewController::class, 'show'])
    ->whereUuid('uuid')
    ->whereNumber('w');
