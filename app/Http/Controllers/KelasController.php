<?php

namespace App\Http\Controllers;

use App\Models\Kehadiran;
use App\Models\Kelas;
use App\Models\KetidakHadiran;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function App\Http\Controllers\API\getBetweenDates;
use function PHPUnit\Framework\isEmpty;

class KelasController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kelas = Kelas::all();
        $tahun = Date('Y');
        $bulan = Date('n');
        // $bulan_sekarang = Date('m');
        if ($bulan >= 6) {
            $tahungenap = $tahun + 1;
            $tahunganjil = $tahun;
        } else {
            $tahungenap = $tahun - 1;
            $tahunganjil = $tahun;
        }
        return view('kelas.daftarkelas', compact('kelas', 'tahunganjil', 'tahungenap'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $User = User::where('level', 2)->get();
        return view('kelas.buatkelas', compact('User'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $kelas = new Kelas();
        $kelas->nama_kelas = $request->input('nama_kelas');
        $kelas->id_guru = $request->input('id_guru');
        $kelas->jumlah_siswa = 0;
        $kelas->detail_kelas = $request->input('detail_kelas');
        $kelas->kode_kelas = Str::random(5);
        if ($kelas->save()) {
            return redirect()->back()->with('success', 'kelas berhasil ditambahkan');
        }
        return redirect()->back()->with('failed', 'data kelas gagal ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Kelas  $kelas
     * @return \Illuminate\Http\Response
     */
    public function show(Kelas $kelas)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Kelas  $kelas
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $kelas = Kelas::find($id);
        return view('kelas.editkelas', compact('kelas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Kelas  $kelas
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $kelas = Kelas::find($id);
        $kelas->nama_kelas = $request->input('nama_kelas');
        $kelas->id_guru = $request->input('id_guru');
        $kelas->detail_kelas = $request->input('detail_kelas');
        if ($kelas->save()) {
            return redirect()->back()->with('success', 'kelas berhasil diupdate');
        }
        return redirect()->back()->with('failed', 'data kelas gagal diupdate');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Kelas  $kelas
     * @return \Illuminate\Http\Response
     */

    public function getBetweenDates($startDate, $endDate)
    {
        $rangArray = [];

        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);

        for (
            $currentDate = $startDate;
            $currentDate <= $endDate;
            $currentDate += (86400)
        ) {

            $date = date('Y-m-d , D', $currentDate);
            // $hari = date('D', $currentDate);
            $rangArray[] = $date;
            // foreach ($rangArray as $array) {
            //    if ($hari == 'Sun') {

            //    }
            // }
        }
        return $rangArray;
    }
    public function destroy($id)
    {

        $siswa = User::where('level', 2)->where('id_kelas', $id)->get();
        $siswa->id_kelas = 0;

        if (Kelas::destroy($id)) {
            if ($siswa->save()) {
                return redirect()->back()->with('deleted', 'kelas berhasil dihapus');
            }
        }
        return redirect()->back()->with('delete-failed', 'kelas tidak dapat dihapus');
    }

    public function open($id, Request $req)
    {
        $menit = $req->input('menit');
        $now = Date('Y-m-d H:i:s');
        $hours = '00:' . $menit . ':10';

        $d0 = strtotime(date('Y-m-d 00:00:00'));
        $d1 = strtotime(date('Y-m-d ') . $hours);

        $sumTime = strtotime($now) + ($d1 - $d0);
        $new_time = date("Y-m-d H:i:s", $sumTime);

        $kelas = Kelas::find($id)->first();
        $kelas->close_time = $new_time;
        $kelas->status = 1;
        if ($kelas->save()) {
            return redirect()->back()->with('opened', 'kelas berhasil dibuka, jangan lupa untuk ditutup', ['kelas' => $kelas]);
        }
        return redirect()->back()->with('failed-opened', 'kelas gagal dibuka, silahkan coba lagi');
    }

    public function close($id)
    {
        $kelas = Kelas::find($id);
        $kelas->status = 0;
        if ($kelas->save()) {
            return redirect()->back()->with('success', 'kelas berhasil ditutup');
        }
        return redirect()->back()->with('failed-closed', 'kelas gagal dibuka, silahkan coba lagi');
    }

    public function daftarGuru()
    {
        $guru = User::where('level', 2)->orderBy('id', 'asc')->paginate(5);

        return view('user.daftar_guru', compact('guru'));
    }

    public function daftarSiswa()
    {
        $siswa = User::where('level', 1)->orderBy('id', 'asc')->paginate(8);

        return view('user.daftar_siswa', compact('siswa'));
    }

    public function downloadRekapPerbulan(Request $req)
    {


        $id = $req->input('id_kelas');
        $data_kelas = Kelas::find($id);
        $bulan = $req->input('kategori');
        if ($bulan == 1) {
            $bulan_terbilang = 'januari';
        } else if ($bulan == 2) {
            $bulan_terbilang = 'februari';
        } else if ($bulan == 3) {
            $bulan_terbilang = 'maret';
        } else if ($bulan == 4) {
            $bulan_terbilang = 'april';
        } else if ($bulan == 5) {
            $bulan_terbilang = 'mei';
        } else if ($bulan == 6) {
            $bulan_terbilang = 'juni';
        } else if ($bulan == 7) {
            $bulan_terbilang = 'juli';
        } else if ($bulan == 8) {
            $bulan_terbilang = 'agustus';
        } else if ($bulan == 9) {
            $bulan_terbilang = 'september';
        } else if ($bulan == 10) {
            $bulan_terbilang = 'oktober';
        } else if ($bulan == 11) {
            $bulan_terbilang = 'november';
        } else {
            $bulan_terbilang = 'desember';
        }


        if ($bulan < 10) {
            $bulan = '0' . $bulan;
        }


        $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, Date('Y'));
        $startdate = DateTime::createFromFormat('Ymd', Date('Y')  . $bulan . '1')->format('Y-m-d');
        $enddate = DateTime::createFromFormat('Ymd', Date('Y')  . $bulan . $jumlah_hari)->format('Y-m-d');
        $tanggal_presensi = $this->getBetweenDates($startdate, $enddate);

        $first = Kehadiran::select('user_id', 'nama_siswa', DB::raw(" 'hadir' as keterangan"),  'created_at')
            ->whereMonth('created_at', $bulan)->whereYear('created_at', date('Y'))->where('id_kelas', $id);
        $daftar_presensi = KetidakHadiran::select('user_id', 'nama_siswa', DB::raw('keterangan'),  'created_at')
            ->whereMonth('created_at', $bulan)->whereYear('created_at', date('Y'))->where('id_kelas', $id)
            ->union($first)->orderBy('created_at', 'desc')->get()->unique('nama_siswa');

        $daftar_siswa = KetidakHadiran::select('user_id', 'nama_siswa', DB::raw('keterangan'),  'created_at')
            ->whereMonth('created_at', $bulan)->whereYear('created_at', date('Y'))->where('id_kelas', $id)
            ->union($first)->orderBy('created_at', 'desc')->pluck('user_id');
        // $id_siswa = array($daftar_siswa);
        $list_id_siswa = $daftar_siswa->unique();

        $period = new DatePeriod(new DateTime($startdate), new DateInterval('P1D'), new DateTime($enddate . '+1 day'));
        foreach ($period as $date) {
            $dates[] = $date->format("Y-m-d");
        }

        $result = [];
        foreach ($list_id_siswa as $siswa) {
            foreach ($dates as $date) {
                $kehadiran = Kehadiran::select('user_id', 'nama_siswa', DB::raw(" 'hadir' as keterangan"),  'created_at')
                    ->whereDate('created_at', $date)->where('user_id', $siswa)->where('id_kelas', $id);
                $daftar_kehadiran = KetidakHadiran::select('user_id', 'nama_siswa', DB::raw('keterangan'),  'created_at')
                    ->whereDate('created_at', $date)->where('user_id', $siswa)->where('id_kelas', $id)
                    ->union($kehadiran)->orderBy('created_at', 'desc')->pluck('keterangan');
                if (!$daftar_kehadiran->isEmpty()) {
                    if ($daftar_kehadiran->all() == ['hadir']) {
                        $result[$siswa][$date] = ('h');
                    } elseif ($daftar_kehadiran->all() == ['sakit']) {
                        $result[$siswa][$date] = ('s');
                    } elseif ($daftar_kehadiran->all() == ['izin']) {
                        $result[$siswa][$date] = ('i');
                    } elseif ($daftar_kehadiran->all() == ['sakit']) {
                        $result[$siswa][$date] = ('a');
                    }
                } else {
                    $result[$siswa][$date] = ('');
                }
            }
        }

        $daftar_kehadiran_perbulan = $result;

        if (!$daftar_presensi->isEmpty()) {
            $pdf = PDF::loadView('kelas.rekap_presensi_perbulan', [
                'daftar_presensi' => $daftar_presensi, 'jumlah_hari' => $jumlah_hari, 'data_kelas' => $data_kelas, 'tanggal_presensi' => $tanggal_presensi, 'bulan' => $bulan_terbilang, 'presensi_perbulan' => $daftar_kehadiran_perbulan
            ]);
            $pdf->setPaper('A3', 'landscape');

            return $pdf->stream('rekap_presensi_bulan_' . $bulan . '.pdf');
        } else {
            return redirect()->back()->with('failed', 'data presensi tidak ditemukan');
        }
    }

    public function downloadRekapPerbulanAPI($id, $bulan)
    {
        $data_kelas = Kelas::find($id);
        if ($bulan == 1) {
            $bulan_terbilang = 'januari';
        } else if ($bulan == 2) {
            $bulan_terbilang = 'februari';
        } else if ($bulan == 3) {
            $bulan_terbilang = 'maret';
        } else if ($bulan == 4) {
            $bulan_terbilang = 'april';
        } else if ($bulan == 5) {
            $bulan_terbilang = 'mei';
        } else if ($bulan == 6) {
            $bulan_terbilang = 'juni';
        } else if ($bulan == 7) {
            $bulan_terbilang = 'juli';
        } else if ($bulan == 8) {
            $bulan_terbilang = 'agustus';
        } else if ($bulan == 9) {
            $bulan_terbilang = 'september';
        } else if ($bulan == 10) {
            $bulan_terbilang = 'oktober';
        } else if ($bulan == 11) {
            $bulan_terbilang = 'november';
        } else {
            $bulan_terbilang = 'desember';
        }

        if ($bulan < 10) {
            $bulan = '0' . $bulan;
        }


        $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, Date('Y'));
        $startdate = DateTime::createFromFormat('Ymd', Date('Y')  . $bulan . '1')->format('Y-m-d');
        $enddate = DateTime::createFromFormat('Ymd', Date('Y')  . $bulan . $jumlah_hari)->format('Y-m-d');
        $tanggal_presensi = $this->getBetweenDates($startdate, $enddate);

        $first = Kehadiran::select('user_id', 'nama_siswa', DB::raw(" 'hadir' as keterangan"),  'created_at')
            ->whereMonth('created_at', $bulan)->whereYear('created_at', date('Y'))->where('id_kelas', $id);
        $daftar_presensi = KetidakHadiran::select('user_id', 'nama_siswa', DB::raw('keterangan'),  'created_at')
            ->whereMonth('created_at', $bulan)->whereYear('created_at', date('Y'))->where('id_kelas', $id)
            ->union($first)->orderBy('created_at', 'desc')->get()->unique('nama_siswa');

        $daftar_siswa = KetidakHadiran::select('user_id', 'nama_siswa', DB::raw('keterangan'),  'created_at')
            ->whereMonth('created_at', $bulan)->whereYear('created_at', date('Y'))->where('id_kelas', $id)
            ->union($first)->orderBy('created_at', 'desc')->pluck('user_id');
        // $id_siswa = array($daftar_siswa);
        $list_id_siswa = $daftar_siswa->unique();

        $period = new DatePeriod(new DateTime($startdate), new DateInterval('P1D'), new DateTime($enddate . '+1 day'));
        foreach ($period as $date) {
            $dates[] = $date->format("Y-m-d");
        }

        $result = [];
        foreach ($list_id_siswa as $siswa) {
            foreach ($dates as $date) {
                $kehadiran = Kehadiran::select('user_id', 'nama_siswa', DB::raw(" 'hadir' as keterangan"),  'created_at')
                    ->whereDate('created_at', $date)->where('user_id', $siswa)->where('id_kelas', $id);
                $daftar_kehadiran = KetidakHadiran::select('user_id', 'nama_siswa', DB::raw('keterangan'),  'created_at')
                    ->whereDate('created_at', $date)->where('user_id', $siswa)->where('id_kelas', $id)
                    ->union($kehadiran)->orderBy('created_at', 'desc')->pluck('keterangan');
                if (!$daftar_kehadiran->isEmpty()) {
                    if ($daftar_kehadiran->all() == ['hadir']) {
                        $result[$siswa][$date] = ('h');
                    } elseif ($daftar_kehadiran->all() == ['sakit']) {
                        $result[$siswa][$date] = ('s');
                    } elseif ($daftar_kehadiran->all() == ['izin']) {
                        $result[$siswa][$date] = ('i');
                    } elseif ($daftar_kehadiran->all() == ['sakit']) {
                        $result[$siswa][$date] = ('a');
                    }
                } else {
                    $result[$siswa][$date] = ('');
                }
            }
        }

        $daftar_kehadiran_perbulan = $result;
        // return $result[1]["2022-03-01"];

        if (!$daftar_presensi->isEmpty()) {
            $pdf = PDF::loadView('kelas.rekap_presensi_perbulan', [
                'daftar_presensi' => $daftar_presensi, 'jumlah_hari' => $jumlah_hari, 'data_kelas' => $data_kelas, 'tanggal_presensi' => $tanggal_presensi, 'bulan' => $bulan_terbilang, 'presensi_perbulan' => $daftar_kehadiran_perbulan
            ]);
            $pdf->setPaper('A3', 'landscape');

            return $pdf->stream('rekap_presensi_bulan_' . $bulan . '.pdf');
        } else {
            return redirect()->back()->with('failed', 'data presensi tidak ditemukan');
        }
    }

    public function downloadRekapPersemester(Request $req)
    {
        $id = $req->input('id_kelas');
        $data_kelas = Kelas::find($id);
        $full_kategori = $req->input('kategori');
        $full_kategori_pieces = explode(" ", $full_kategori);
        $kategori = $full_kategori_pieces[0];
        $tahun_ganjil = $full_kategori_pieces[1];
        $tahun_genap = $full_kategori_pieces[2];
        $fromganjil = DateTime::createFromFormat('Ymd', $tahun_ganjil  . '07' . '1')->format('Y-m-d');
        $toganjil = DateTime::createFromFormat('Ymd', $tahun_ganjil  . '12' . '31')->format('Y-m-d');
        $fromgenap = DateTime::createFromFormat('Ymd', $tahun_genap  . '01' . '1')->format('Y-m-d');
        $togenap = DateTime::createFromFormat('Ymd', $tahun_genap  . '06' . '31')->format('Y-m-d');

        $d1ganjil = new DateTime($toganjil);
        $d2ganjil = new DateTime($fromganjil);
        $d1genap = new DateTime($togenap);
        $d2genap = new DateTime($fromgenap);
        // $interval_bulan_ganjil = ($d1->diff($d2)->m);
        // return $interval_bulan_ganjil;
        $interval = DateInterval::createFromDateString('1 month');
        $periodganjil = new DatePeriod($d2ganjil, $interval, $d1ganjil);
        $periodegenap = new DatePeriod($d2genap, $interval, $d1genap);

        $interval_ganjil = [];
        $interval_genap = [];
        foreach ($periodganjil as $p) {
            $interval_ganjil[] = ($p->format('Y-m'));
        }
        foreach ($periodegenap as $p) {
            $interval_genap[] = ($p->format('Y-m'));
        }

        $result = [];


        if ($kategori == 'ganjil') {
            $first = Kehadiran::select('user_id', 'nama_siswa', DB::raw(" 'hadir' as keterangan"), 'created_at')->where('id_kelas', $id)
                ->whereBetween('created_at', [$fromganjil, $toganjil]);
            $daftar_siswa = KetidakHadiran::select('user_id', 'nama_siswa', DB::raw('keterangan'), 'created_at')->where('id_kelas', $id)
                ->whereBetween('created_at', [$fromganjil, $toganjil])
                ->union($first)->orderBy('nama_siswa', 'desc')->get()->unique('user_id');

            foreach ($interval_ganjil as $ganjil) {
                $bulan = DateTime::createFromFormat('Y-m', $ganjil)->format('m');
                $tahun = Date::createFromFormat('Y-m', $ganjil)->format('Y');
                $id_siswa = Kehadiran::select('user_id')->where('id_kelas', $id)
                    ->whereBetween('created_at', [$fromganjil, $toganjil]);
                $daftar_id_siswa = KetidakHadiran::select('user_id')->where('id_kelas', $id)
                    ->whereBetween('created_at', [$fromganjil, $toganjil])
                    ->union($id_siswa)->pluck('user_id');
                $list_id_siswa = $daftar_id_siswa->unique();
                foreach ($list_id_siswa as $siswa) {
                    $hadir = Kehadiran::whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->where('user_id', $siswa)
                        ->where('id_kelas', $id)->count();
                    $sakit = KetidakHadiran::whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->where('user_id', $siswa)
                        ->where('id_kelas', $id)->where('keterangan', 'sakit')->count();
                    $izin = KetidakHadiran::whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->where('user_id', $siswa)
                        ->where('id_kelas', $id)->where('keterangan', 'izin')->count();
                    $alpha = KetidakHadiran::whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->where('user_id', $siswa)
                        ->where('id_kelas', $id)->where('keterangan', 'alpha')->count();
                    $result[$siswa][$ganjil] = array('h' => $hadir, 's' => $sakit, 'i' => $izin, 'a' => $alpha);
                }
            }
            $data_presensi = $result;
            // return $data_presensi[1]['2021-08']['h'];
        } else {
            $first = Kehadiran::select('user_id', 'nama_siswa', DB::raw(" 'hadir' as keterangan"), 'created_at')->where('id_kelas', $id)
                ->whereBetween('created_at', [$fromgenap, $togenap]);
            $daftar_siswa = KetidakHadiran::select('user_id', 'nama_siswa', DB::raw('keterangan'), 'created_at')->where('id_kelas', $id)
                ->whereBetween('created_at', [$fromgenap, $togenap])
                ->union($first)->orderBy('nama_siswa', 'desc')->get()->unique('user_id');

            foreach ($interval_genap as $genap) {
                $bulan = DateTime::createFromFormat('Y-m', $genap)->format('m');
                $tahun = Date::createFromFormat('Y-m', $genap)->format('Y');
                $id_siswa = Kehadiran::select('user_id')->where('id_kelas', $id)
                    ->whereBetween('created_at', [$fromgenap, $togenap]);
                $daftar_id_siswa = KetidakHadiran::select('user_id')->where('id_kelas', $id)
                    ->whereBetween('created_at', [$fromgenap, $togenap])
                    ->union($id_siswa)->pluck('user_id');
                $list_id_siswa = $daftar_id_siswa->unique();
                foreach ($list_id_siswa as $siswa) {
                    $hadir = Kehadiran::whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->where('user_id', $siswa)
                        ->where('id_kelas', $id)->count();
                    $sakit = KetidakHadiran::whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->where('user_id', $siswa)
                        ->where('id_kelas', $id)->where('keterangan', 'sakit')->count();
                    $izin = KetidakHadiran::whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->where('user_id', $siswa)
                        ->where('id_kelas', $id)->where('keterangan', 'izin')->count();
                    $alpha = KetidakHadiran::whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->where('user_id', $siswa)
                        ->where('id_kelas', $id)->where('keterangan', 'alpha')->count();
                    $result[$siswa][$genap] = array('h' => $hadir, 's' => $sakit, 'i' => $izin, 'a' => $alpha);
                }
            }
            $data_presensi = $result;
        }
        if ($kategori == 'ganjil') {
            if (!$daftar_siswa->isEmpty()) {
                $pdf = PDF::loadView('kelas.rekap_presensi_persemester', ['data_kelas' => $data_kelas, 'daftar_siswa' => $daftar_siswa, 'kategori' => $kategori, 'tahun_ajaran' => $tahun_ganjil . '/' . $tahun_genap, 'interval' => $interval_ganjil, 'data_presensi' => $data_presensi]);
                $pdf->setPaper('A3', 'landscape');

                return $pdf->stream('rekap_presensi_semester_' . $kategori . '.pdf');
            } else {
                return redirect()->back()->with('failed', 'data presensi tidak ditemukan');
            }
        } else {
            if (!$daftar_siswa->isEmpty()) {
                $pdf = PDF::loadView('kelas.rekap_presensi_persemester', ['data_kelas' => $data_kelas, 'daftar_siswa' => $daftar_siswa, 'kategori' => $kategori, 'tahun_ajaran' => $tahun_ganjil . '/' . $tahun_genap, 'interval' => $interval_genap, 'data_presensi' => $data_presensi]);
                $pdf->setPaper('A3', 'landscape');

                return $pdf->stream('rekap_presensi_semester_' . $kategori . '.pdf');
            } else {
                return redirect()->back()->with('failed', 'data presensi tidak ditemukan');
            }
        }
    }

    public function downloadRekapPersemesterAPI($id, $kategori, $tahun_ganjil, $tahun_genap)
    {
        // $id = $req->input('id_kelas');
        $data_kelas = Kelas::find($id);
        // $kategori = $req->input('kategori');
        // $tahun_ganjil = $req->input('tahun_ganjil');
        // $tahun_genap = $req->input('tahun_genap');
        $fromganjil = DateTime::createFromFormat('Ymd', $tahun_ganjil  . '07' . '1')->format('Y-m-d');
        $toganjil = DateTime::createFromFormat('Ymd', $tahun_ganjil  . '12' . '31')->format('Y-m-d');
        $fromgenap = DateTime::createFromFormat('Ymd', $tahun_genap  . '01' . '1')->format('Y-m-d');
        $togenap = DateTime::createFromFormat('Ymd', $tahun_genap  . '06' . '31')->format('Y-m-d');

        $d1ganjil = new DateTime($toganjil);
        $d2ganjil = new DateTime($fromganjil);
        $d1genap = new DateTime($togenap);
        $d2genap = new DateTime($fromgenap);
        // $interval_bulan_ganjil = ($d1->diff($d2)->m);
        // return $interval_bulan_ganjil;
        $interval = DateInterval::createFromDateString('1 month');
        $periodganjil = new DatePeriod($d2ganjil, $interval, $d1ganjil);
        $periodegenap = new DatePeriod($d2genap, $interval, $d1genap);

        $interval_ganjil = [];
        $interval_genap = [];
        foreach ($periodganjil as $p) {
            $interval_ganjil[] = ($p->format('Y-m'));
        }
        foreach ($periodegenap as $p) {
            $interval_genap[] = ($p->format('Y-m'));
        }

        $result = [];


        if ($kategori == 'ganjil') {
            $first = Kehadiran::select('user_id', 'nama_siswa', DB::raw(" 'hadir' as keterangan"), 'created_at')->where('id_kelas', $id)
                ->whereBetween('created_at', [$fromganjil, $toganjil]);
            $daftar_siswa = KetidakHadiran::select('user_id', 'nama_siswa', DB::raw('keterangan'), 'created_at')->where('id_kelas', $id)
                ->whereBetween('created_at', [$fromganjil, $toganjil])
                ->union($first)->orderBy('nama_siswa', 'desc')->get()->unique('user_id');

            foreach ($interval_ganjil as $ganjil) {
                $bulan = DateTime::createFromFormat('Y-m', $ganjil)->format('m');
                $tahun = Date::createFromFormat('Y-m', $ganjil)->format('Y');
                $id_siswa = Kehadiran::select('user_id')->where('id_kelas', $id)
                    ->whereBetween('created_at', [$fromganjil, $toganjil]);
                $daftar_id_siswa = KetidakHadiran::select('user_id')->where('id_kelas', $id)
                    ->whereBetween('created_at', [$fromganjil, $toganjil])
                    ->union($id_siswa)->pluck('user_id');
                $list_id_siswa = $daftar_id_siswa->unique();
                foreach ($list_id_siswa as $siswa) {
                    $hadir = Kehadiran::whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->where('user_id', $siswa)
                        ->where('id_kelas', $id)->count();
                    $sakit = KetidakHadiran::whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->where('user_id', $siswa)
                        ->where('id_kelas', $id)->where('keterangan', 'sakit')->count();
                    $izin = KetidakHadiran::whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->where('user_id', $siswa)
                        ->where('id_kelas', $id)->where('keterangan', 'izin')->count();
                    $alpha = KetidakHadiran::whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->where('user_id', $siswa)
                        ->where('id_kelas', $id)->where('keterangan', 'alpha')->count();
                    $result[$siswa][$ganjil] = array('h' => $hadir, 's' => $sakit, 'i' => $izin, 'a' => $alpha);
                }
            }
            $data_presensi = $result;
            // return $data_presensi[1]['2021-08']['h'];
        } else {
            $first = Kehadiran::select('user_id', 'nama_siswa', DB::raw(" 'hadir' as keterangan"), 'created_at')->where('id_kelas', $id)
                ->whereBetween('created_at', [$fromgenap, $togenap]);
            $daftar_siswa = KetidakHadiran::select('user_id', 'nama_siswa', DB::raw('keterangan'), 'created_at')->where('id_kelas', $id)
                ->whereBetween('created_at', [$fromgenap, $togenap])
                ->union($first)->orderBy('nama_siswa', 'desc')->get()->unique('user_id');

            foreach ($interval_genap as $genap) {
                $bulan = DateTime::createFromFormat('Y-m', $genap)->format('m');
                $tahun = Date::createFromFormat('Y-m', $genap)->format('Y');
                $id_siswa = Kehadiran::select('user_id')->where('id_kelas', $id)
                    ->whereBetween('created_at', [$fromgenap, $togenap]);
                $daftar_id_siswa = KetidakHadiran::select('user_id')->where('id_kelas', $id)
                    ->whereBetween('created_at', [$fromgenap, $togenap])
                    ->union($id_siswa)->pluck('user_id');
                $list_id_siswa = $daftar_id_siswa->unique();
                foreach ($list_id_siswa as $siswa) {
                    $hadir = Kehadiran::whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->where('user_id', $siswa)
                        ->where('id_kelas', $id)->count();
                    $sakit = KetidakHadiran::whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->where('user_id', $siswa)
                        ->where('id_kelas', $id)->where('keterangan', 'sakit')->count();
                    $izin = KetidakHadiran::whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->where('user_id', $siswa)
                        ->where('id_kelas', $id)->where('keterangan', 'izin')->count();
                    $alpha = KetidakHadiran::whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->where('user_id', $siswa)
                        ->where('id_kelas', $id)->where('keterangan', 'alpha')->count();
                    $result[$siswa][$genap] = array('h' => $hadir, 's' => $sakit, 'i' => $izin, 'a' => $alpha);
                }
            }
            $data_presensi = $result;
        }
        if ($kategori == 'ganjil') {
            if (!$daftar_siswa->isEmpty()) {
                $pdf = PDF::loadView('kelas.rekap_presensi_persemester', ['data_kelas' => $data_kelas, 'daftar_siswa' => $daftar_siswa, 'kategori' => $kategori, 'tahun_ajaran' => $tahun_ganjil . '/' . $tahun_genap, 'interval' => $interval_ganjil, 'data_presensi' => $data_presensi]);
                $pdf->setPaper('A3', 'landscape');

                return $pdf->stream('rekap_presensi_semester_' . $kategori . '.pdf');
            } else {
                return redirect()->back()->with('failed', 'data presensi tidak ditemukan');
            }
        } else {
            if (!$daftar_siswa->isEmpty()) {
                $pdf = PDF::loadView('kelas.rekap_presensi_persemester', ['data_kelas' => $data_kelas, 'daftar_siswa' => $daftar_siswa, 'kategori' => $kategori, 'tahun_ajaran' => $tahun_ganjil . '/' . $tahun_genap, 'interval' => $interval_genap, 'data_presensi' => $data_presensi]);
                $pdf->setPaper('A3', 'landscape');

                return $pdf->stream('rekap_presensi_semester_' . $kategori . '.pdf');
            } else {
                return redirect()->back()->with('failed', 'data presensi tidak ditemukan');
            }
        }
    }
}
