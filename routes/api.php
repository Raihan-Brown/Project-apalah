<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// Pastikan baris import ini ada! Sesuaikan nama controllermu
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\PublicApiController;
use App\Http\Controllers\Api\PpdbApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ============================================================
// 1. PUBLIC ROUTES (Bisa diakses TANPA Login/Token)
// ============================================================

// Auth (Login & Register WAJIB di sini, jangan masuk middleware!)
Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/register', [AuthApiController::class, 'register']);

// Data Umum (Berita, Guru, Galeri, dll)
Route::get('/berita', [PublicApiController::class, 'getBerita']);
Route::get('/berita/{slug}', [PublicApiController::class, 'detailBerita']);
Route::get('/guru', [PublicApiController::class, 'getGuru']);
Route::get('/ekskul', [PublicApiController::class, 'getEkskul']);
Route::get('/kegiatan', [PublicApiController::class, 'getKegiatan']);
Route::get('/prestasi', [PublicApiController::class, 'getPrestasi']);
Route::get('galeri', [PublicApiController::class, 'getGaleri']);
Route::post('galeri/store', [PublicApiController::class, 'storeGaleri']);
Route::delete('galeri/{id}', [PublicApiController::class, 'deleteGaleri']);
Route::get('/lokasi', [PublicApiController::class, 'getLokasi']);

// ============================================================
// 2. PROTECTED ROUTES (Harus Login & Punya Token)
// ============================================================
Route::middleware('auth:sanctum')->group(function () {
    
    // Cek User Profile
    Route::get('/user', [AuthApiController::class, 'me']);
    
    // Logout
    Route::post('/logout', [AuthApiController::class, 'logout']);

    // Form PPDB (Kirim Data)
    // Kalau mau PPDB bisa diakses tanpa login, pindahin ke grup PUBLIC di atas
    Route::post('/ppdb', [PpdbApiController::class, 'store']);

    // ADMIN CRUD (Kelola Berita)
    Route::post('/berita/store', [PublicApiController::class, 'storeBerita']);
    Route::post('/berita/update/{id}', [PublicApiController::class, 'updateBerita']);
    Route::delete('/berita/{id}', [PublicApiController::class, 'deleteBerita']);
    
});