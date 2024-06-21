<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PageAddons;

Route::get('/', [DownloadController::class, 'index']);
Route::post('/download', [DownloadController::class, 'download']);
Route::post('/downloadVideo', [DownloadController::class, 'downloadVideo']);
Route::get('/download-file/{file_name}', [DownloadController::class, 'getFile'])->name('download.file');

Route::get('/comments', [CommentController::class, 'index']);
Route::post('/comments', [CommentController::class, 'store']);
Route::get('/faq', [PageAddons::class, 'faq']);