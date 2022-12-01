<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use Illuminate\Http\Request;
use App\Models\KetidakHadiran as KetidakHadiran;
use App\Models\User;
use Faker\Provider\Image;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

class KetidakHadiranController extends Controller
{
    public function tidakHadir(Request $req)
    {
        $tidakHadir = new KetidakHadiran();
        $tidakHadir->id_kelas = $req->input('id_kelas');
        $tidakHadir->user_id = $req->input('user_id');
        $tidakHadir->nama_siswa = $req->input('nama_siswa');
        $tidakHadir->keterangan = $req->input('keterangan');
        $tidakHadir->detail = $req->input('detail');
        $tidakHadir->surat_bukti_url = "";
        $tidakHadir->lokasi = $req->input('lokasi');

        if ($tidakHadir->save()) {
            // return response(['result' => true, 'data' => $tidakHadir]);
            if ($req->hasFile('image')) {
                $path = $req->file('image')->store('ketidakhadirans');
                $tidakHadir->surat_bukti_url = 'http://blog-api.zaki-alwan.xyz/storage/' . $path;
            }
            $tidakHadir->save();
            return response(['result' => true, 'data' => $tidakHadir]);
        } else {
            return response(['result' => false, 'data' => 'gagal menambahkan data ketidakhadiran']);
        }
    }

    public function daftarTidakHadir(Request $req)
    {
        $id_kelas = $req->input('id_kelas');
        $keterangan = $req->input('keterangan');
        $tanggal = $req->input('tanggal');
        $tidak_hadir = KetidakHadiran::where('id_kelas', $id_kelas)->whereDate('created_at', $tanggal)->where('keterangan', $keterangan)->get();
        $count = KetidakHadiran::where('id_kelas', $id_kelas)->whereDate('created_at', $tanggal)->where('keterangan', $keterangan)->count();
        if (!$tidak_hadir->isEmpty()) {
            return response(['result' => true, 'count' => $count, 'data' => $tidak_hadir]);
        }
        return response(['result' => false, 'data' => 'data tidak ditemukan']);
    }

    public function daftarAlpha(Request $req)
    {
        $id_kelas = $req->input('id_kelas');
        $tanggal = $req->input('tanggal');
        $alpha = User::select('id', 'name', 'id_kelas')
            ->whereNotExists(function ($query) use ($tanggal) {
                $query->select(DB::raw(1))->from('kehadirans')->whereRaw('users.id = kehadirans.user_id')
                    ->whereDate('kehadirans.created_at', $tanggal);
            })
            ->whereNotExists(function ($query) use ($tanggal) {
                $query->select(DB::raw(1))->from('ketidak_hadirans')->whereRaw('users.id = ketidak_hadirans.user_id')
                    ->whereDate('ketidak_hadirans.created_at', $tanggal);
            })->where('id_kelas', $id_kelas)
            ->get();

        $count =  User::select('name')
            ->whereNotExists(function ($query) use ($tanggal) {
                $query->select(DB::raw(1))->from('kehadirans')->whereRaw('users.id = kehadirans.user_id')
                    ->whereDate('kehadirans.created_at', $tanggal);
            })
            ->whereNotExists(function ($query) use ($tanggal) {
                $query->select(DB::raw(1))->from('ketidak_hadirans')->whereRaw('users.id = ketidak_hadirans.user_id')
                    ->whereDate('ketidak_hadirans.created_at', $tanggal);
            })->where('id_kelas', $id_kelas)
            ->count();

        // $tes2 = User::select('name')->leftJoin('kehadirans','users.id','=','kehadirans.id')->

        if (!$alpha->isEmpty()) {
            return response(['result' => true, 'count' => $count, 'data' => $alpha]);
        } else {
            return response(['result' => false]);
        }
    }
}
