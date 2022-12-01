<?php

use App\Http\Controllers\API\PresensiController;
use App\Http\Controllers\API\KelasController;
use App\Http\Controllers\API\KetidakHadiranController;
use App\Http\Controllers\API\SliderController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('reset-email', [UserController::class, 'sendResetEmail']);
Route::get('sliders', [SliderController::class, 'index']);
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::post('logout', [UserController::class, 'logout']);
Route::post('logout-guru', [UserController::class, 'logoutGuru']);
Route::post('enter-class', [UserController::class, 'enterClass']);
Route::post('set-pin', [UserController::class, 'setUserPin']);
Route::post('check-pin', [UserController::class, 'checkUserPin']);
Route::post('check-available-pin', [UserController::class, 'checkAvailablePin']);
Route::post('update-user-name', [UserController::class, 'updateUserName']);
Route::post('update-user-email', [UserController::class, 'updateUserEmail']);
Route::post('set-new-password', [UserController::class, 'setNewPassword']);
Route::post('data-kelas', [KelasController::class, 'dataKelas']);
Route::post('buat-kelas', [KelasController::class, 'buatKelas']);
Route::post('daftar-kelas', [KelasController::class, 'daftarKelas']);
Route::post('daftar-hadir', [KelasController::class, 'daftarHadir']);
Route::post('download-rekap', [KelasController::class, 'downloadRekap']);
Route::post('open-kelas/{id}', [KelasController::class, 'openKelas']);
Route::post('daftar-alpha', [KetidakHadiranController::class, 'daftarAlpha']);
Route::post('cek-presensi', [KelasController::class, 'cekPresensi']);
Route::post('daftar-siswa', [KelasController::class, 'daftarSiswa']);
Route::post('kick-siswa', [KelasController::class, 'kickSiswa']);
Route::post('cek-rekap-bulan', [KelasController::class, 'cekRekapPresensiBulan']);
Route::post('cek-rekap-semester', [KelasController::class, 'cekRekapPresensiSemester']);
Route::post('presensi', [PresensiController::class, 'hadir']);
Route::post('daftar-presensi', [PresensiController::class, 'daftarPresensi']);
Route::post('tidak-hadir', [KetidakHadiranController::class, 'tidakHadir']);
Route::post('daftar-tidak-hadir', [KetidakHadiranController::class, 'daftarTidakHadir']);
