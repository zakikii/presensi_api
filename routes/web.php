<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SliderController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth/login');
});

Route::get('dashboard', [DashboardController::class, 'index']);
Route::get('buat-kelas', [KelasController::class, 'create']);
Route::post('simpan-kelas', [KelasController::class, 'store']);
Route::get('daftar-kelas', [KelasController::class, 'index']);
Route::get('edit-kelas/{id}', [KelasController::class, 'edit']);
Route::post('update-kelas/{id}', [KelasController::class, 'update']);
Route::get('delete-kelas/{id}', [KelasController::class, 'destroy']);
Route::post('open-kelas/{id}', [KelasController::class, 'open']);
Route::get('close-kelas/{id}', [KelasController::class, 'close']);
Route::get('daftar-guru', [KelasController::class, 'daftarGuru']);
Route::get('daftar-siswa', [KelasController::class, 'daftarSiswa']);
Route::post('download-rekap/bulan', [KelasController::class, 'downloadRekapPerbulan']);
Route::get('download-rekap/bulan/{id}/{bulan}', [KelasController::class, 'downloadRekapPerbulanAPI']);
Route::post('download-rekap/semester', [KelasController::class, 'downloadRekapPersemester']);
Route::get('download-rekap/semester/{id}/{kategori}/{tahun_ganjil}/{tahun_genap}', [KelasController::class, 'downloadRekapPersemesterAPI']);
Route::get('buat-slider', [SliderController::class, 'create']);
Route::get('daftar-slider', [SliderController::class, 'index']);
Route::get('edit-slider/{id}', [SliderController::class, 'edit']);
Route::post('simpan-slider', [SliderController::class, 'store']);
Route::post('update-slider/{id}', [SliderController::class, 'update']);
Route::get('delete-slider/{id}', [SliderController::class, 'destroy']);
Route::get('clear-cache', function () {
    Artisan::call('cache:clear');
});
Route::get('get-pdf', function () {
    Artisan::call('require barryvdh/laravel-dompdf');
});

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
