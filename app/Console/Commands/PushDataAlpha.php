<?php

namespace App\Console\Commands;

use App\Models\Kelas;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;

class PushDataAlpha extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'presensi:harian';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload Data Alpha Siswa';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $daftar_kelas = Kelas::select('id')->where('Status', 1)->pluck('id');

        foreach ($daftar_kelas as $kelas) {
            $kelas->status = 0;
            $kelas->save();
        }
        $this->info('Semua kelas berhasil ditutup');
    }
}
