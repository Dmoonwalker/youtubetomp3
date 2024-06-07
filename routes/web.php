<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DownloadController;

Route::get('/', [DownloadController::class, 'index']);
Route::post('/download', [DownloadController::class, 'download']);
Route::post('/downloadVideo', [DownloadController::class, 'downloadVideo']);
Route::get('/download-file/{file_name}', [DownloadController::class, 'getFile'])->name('download.file');
