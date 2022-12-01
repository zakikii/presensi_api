<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use App\Models\Kelas;
use App\Models\KetidakHadiran;
use App\Models\User;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PDF;

use function PHPUnit\Framework\isEmpty;

class KelasController extends Controller
{
    public function dataKelas(Request $req)
    {

        $idKelas = $req->input('id_kelas');
        $kelas = Kelas::where('id', $idKelas)->first();
        $guru = Kelas::where('id', $idKelas)->first();
        $nama_guru = $guru->guru['name'];
        if ($kelas) {
            return response(['result' => true, 'data' => $kelas, 'nama_guru' => $nama_guru]);
        }
        return response(['result' => false, 'data' => 'data not found']);
    }

    public function buatKelas(Request $req)
    {
        $kelas = new Kelas();
        $kelas->nama_kelas = $req->input('nama_kelas');
        $kelas->id_guru = $req->input('id_guru');
        $kelas->jumlah_siswa = $req->input('jumlah_siswa');
        $kelas->detail_kelas = $req->input('detail_kelas');
        $kelas->status = 0;
        $kelas->kode_kelas = Str::random(5);

        if ($kelas->save()) {
            return response(['result' => true, 'user' => $kelas]);
        }
        return response(['result' => false, 'user' => new Kelas()]);
    }

    public function daftarKelas(Request $req)
    {
        $idGuru = $req->input('id_guru');
        $kelas = Kelas::where('id_guru', $idGuru)->get();

        if (!$kelas->isEmpty()) {
            return response(['result' => true, 'data' => $kelas]);
        }
        return response(['result' => false, 'data' => 'data not found']);
    }

    public function openKelas(Request $req, $id)
    {
        $kelas = Kelas::find($id);
        $kelas->status = $req->input('status');
        if ($kelas->save()) {
            return response(['result' => true, 'data' => $kelas]);
        }
        return response(['result' => false, 'data' => 'kelas gagal dibuka']);
    }

    public function daftarHadir(Request $req)
    {
        $idKelas = $req->input('id_kelas');
        $tanggal = $req->input('tanggal');
        $hadir = Kehadiran::where('id_kelas', $idKelas)->whereDate('created_at', $tanggal)->get();
        $count = Kehadiran::where('id_kelas', $idKelas)->whereDate('created_at', $tanggal)->count();
        if (!$hadir->isEmpty()) {
            return response(['result' => true, 'count' => $count, 'data' => $hadir]);
        }
        return response(['result' => false, 'data' => 'data tidak ditemukan']);
    }
    public function cekPresensi(Request $req)
    {
        $userId = $req->input('user_id');
        $tanggal = date('Y-m-d');
        $hadir = Kehadiran::where('user_id', $userId)->whereDate('created_at', $tanggal)->doesntExist();
        $tidak_hadir = KetidakHadiran::where('user_id', $userId)->whereDate('created_at', $tanggal)->doesntExist();
        if ($hadir == true && $tidak_hadir == true) {
            return response(['result' => true, 'data' => 'user dengan id : ' . $userId . ' dapat mengisi presensi']);
        }
        return response(['result' => false, 'data' => 'user sudah mengisi data presensi']);
    }
    public function cekRekapPresensiBulan(Request $req)
    {
        $id = $req->input('id_kelas');
        $bulan = $req->input('bulan');
        // $tanggal_cek = DateTime::createFromFormat('Ym', Date('Y')  . $bulan )->format('Y-m-d');
        $first = Kehadiran::select('user_id', 'nama_siswa', DB::raw(" 'hadir' as keterangan"),  'created_at')
            ->whereMonth('created_at', $bulan)->whereYear('created_at', date('Y'))->where('id_kelas', $id);
        $daftar_presensi = KetidakHadiran::select('user_id', 'nama_siswa', DB::raw('keterangan'),  'created_at')
            ->whereMonth('created_at', $bulan)->whereYear('created_at', date('Y'))->where('id_kelas', $id)
            ->union($first)->orderBy('created_at', 'desc')->get()->unique('nama_siswa');

        if (!$daftar_presensi->isEmpty()) {
            return response(['result' => true, 'data' => 'kelas memiliki data presensi']);
        }
        return response(['result' => false, 'data' => 'kelas belum memiliki data presensi']);
    }
    public function cekRekapPresensiSemester(Request $req)
    {
        $id = $req->input('id_kelas');
        // $data_kelas = Kelas::find($id);
        $kategori = $req->input('kategori');
        $tahun_ganjil = $req->input('tahun_ganjil');
        $tahun_genap = $req->input('tahun_genap');
        $fromganjil = DateTime::createFromFormat('Ymd', $tahun_ganjil  . '07' . '1')->format('Y-m-d');
        $toganjil = DateTime::createFromFormat('Ymd', $tahun_ganjil  . '12' . '31')->format('Y-m-d');
        $fromgenap = DateTime::createFromFormat('Ymd', $tahun_genap  . '01' . '1')->format('Y-m-d');
        $togenap = DateTime::createFromFormat('Ymd', $tahun_genap  . '06' . '31')->format('Y-m-d');



        if ($kategori == 'ganjil') {
            $first = Kehadiran::select('user_id', 'nama_siswa', DB::raw(" 'hadir' as keterangan"), 'created_at')->where('id_kelas', $id)
                ->whereBetween('created_at', [$fromganjil, $toganjil]);
            $daftar_siswa = KetidakHadiran::select('user_id', 'nama_siswa', DB::raw('keterangan'), 'created_at')->where('id_kelas', $id)
                ->whereBetween('created_at', [$fromganjil, $toganjil])
                ->union($first)->orderBy('nama_siswa', 'desc')->get()->unique('user_id');
            if (!$daftar_siswa->isEmpty()) {
                return response(['result' => true]);
            } else {
                return response(['result' => false, 'data' => 'kelas belum memiliki data presensi']);
            }
        } else {
            $first = Kehadiran::select('user_id', 'nama_siswa', DB::raw(" 'hadir' as keterangan"), 'created_at')->where('id_kelas', $id)
                ->whereBetween('created_at', [$fromgenap, $togenap]);
            $daftar_siswa = KetidakHadiran::select('user_id', 'nama_siswa', DB::raw('keterangan'), 'created_at')->where('id_kelas', $id)
                ->whereBetween('created_at', [$fromgenap, $togenap])
                ->union($first)->orderBy('nama_siswa', 'desc')->get()->unique('user_id');
            if (!$daftar_siswa->isEmpty()) {
                return response(['result' => true]);
            } else {
                return response(['result' => false, 'data' => 'kelas belum memiliki data presensi']);
            }
        }
    }
    public function daftarSiswa(Request $req)
    {
        $idKelas = $req->input('id_kelas');
        $daftar_siswa = User::where('id_kelas', $idKelas)->orderBy('name', 'asc')->get();

        if (!$daftar_siswa->isEmpty()) {
            return response(['result' => true, 'data' => $daftar_siswa]);
        } else {
            return response(['result' => false, 'data' => 'data siswa tidak ditemukan']);
        }
    }
    public function kickSiswa(Request $req)
    {
        $idKelas = $req->input('id_kelas');
        $kelas = Kelas::where('id', $idKelas)->first();
        $kelas->jumlah_siswa_siswa = $kelas->jumlah_siswa_siswa - 1;
        $kelas->save();
        $userId = $req->input('user_id');
        $user = User::find($userId);
        $user->id_kelas = 0;
        $user->save();
        return response(['result' => true, 'data' => $user]);
    }
}
