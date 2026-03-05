<?php

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\ImportController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/redis-test', function () {
    Redis::set('name', 'Laravel Redis OK');
    return Redis::get('name');
});


Route::view('/upload', 'upload');

Route::post('/upload-file', [FileUploadController::class,'upload']);


Route::get('/import', function() {
    return view('import_form');
});

Route::post('/import', [ImportController::class, 'upload'])->name('import.upload');

Route::get('/import-file', function() {
    return view('task-schedule');
});


Route::post('/upload-import',[ImportController::class,'uploadImport']);
