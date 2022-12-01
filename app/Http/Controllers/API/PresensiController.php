<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use App\Models\KetidakHadiran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PresensiController extends Controller
{
    public function hadir(Request $req)
    {
        $kehadiran = new Kehadiran();
        $kehadiran->id_kelas = $req->input('id_kelas');
        $kehadiran->user_id = $req->input('user_id');
        $kehadiran->nama_siswa = $req->input('nama_siswa');
        $kehadiran->lokasi = $req->input('lokasi');

        if ($kehadiran->save()) {
            return response(['result' => true, 'presensi' => $kehadiran]);
        }
        return response(['result' => false, 'presensi' => 'input presensi gagal']);
    }

    public function daftarPresensi(Request $req)
    {
        $from = date('2021-01-01');
        $sekarang = date('Y-m-d');
        $tanggal = date('Y-m-d', strtotime($sekarang . '+ 1 days'));
        $user_id = $req->input('user_id');
        $first = Kehadiran::select('user_id', DB::raw(" 'hadir' as keterangan"), 'created_at', 'lokasi', DB::raw(" null as surat_bukti"))->where('user_id', $user_id)
            ->whereBetween('created_at', [$from, $tanggal]);
        $second = KetidakHadiran::select('user_id', 'keterangan', 'created_at', 'lokasi', 'surat_bukti')->where('user_id', $user_id)
            ->whereBetween('created_at', [$from, $tanggal])
            ->union($first)->orderBy('created_at', 'desc')->get();
        $count = $second->count();
        if (!$second->isEmpty()) {
            return response(['result' => true, 'jumlah data' => $count, 'presensi' => $second]);
        }
    }
}
