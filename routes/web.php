<?php

use App\Http\Controllers\AppTopCategoryController;
use App\Http\Middleware\LogAppTopCategoryRequests;
use App\Http\Middleware\ThrottleAppTopCategory;
use Illuminate\Support\Facades\Route;

Route::get('/appTopCategory', [AppTopCategoryController::class, 'getPositions'])->middleware([ThrottleAppTopCategory::class, LogAppTopCategoryRequests::class]);

Route::get('/', function () {
    return view('welcome');
});
