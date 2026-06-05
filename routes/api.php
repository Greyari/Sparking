<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiLogParkirController;
use App\Http\Controllers\Api\ApiRealtimeController;
use App\Http\Controllers\Api\ApiStatistikController;

// ini di pake di resources\views\user\statistik.blade.php dan kepake juga di resources\views\admin\manageAnalysis.blade.php
Route::get('/statistik-zona', [ApiStatistikController::class,'getStatistik']);

// ini di pake di resources\views\user\realTime.blade.php
Route::get('/get-subzonas/{zonaId}', [ApiRealtimeController::class, 'getSubzonas']);
Route::get('/zona-slot', [ApiRealtimeController::class, 'getZonaslot']);

// ini kepake semua di app.py
Route::get('/real-time/subzona/{subzonaId}', [ApiRealtimeController::class, 'getSubzonaDetails'])->name('realTime.subzonaDetails'); //ini di pake juga di resources\views\user\realTime.blade.php
Route::post('/update-status-slot', [ApiRealtimeController::class, 'updateSlotStatus']);
Route::post('/log-parkir/masuk', [ApiLogParkirController::class, 'masuk']);
Route::post('/log-parkir/keluar', [ApiLogParkirController::class, 'keluar']);
Route::get('/list-subzona', [ApiRealtimeController::class, 'getAllSubzonas']);
