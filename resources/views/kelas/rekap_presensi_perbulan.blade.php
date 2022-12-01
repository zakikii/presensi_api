<!DOCTYPE html>
<html>

<head>
	<title>Rekapitulasi Presensi</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>
	<style type="text/css">
		table tr td,
		table tr th {
			font-size: 9pt;
		}
	</style>
	<center>
		<h5>Daftar Kehadiran Siswa</h4>
			<h5>SMA Negeri 3 Martapura
				</h4>
				<h6>Kota Baru Utara, Martapura, OKU TIMUR
			</h5>
			<h6> {{$bulan}}, {{Date('Y')}}</h6>
	</center>
	&nbsp;
	<table class='table table-bordered'>
		<thead>
			<tr>
				<th colspan="{{$jumlah_hari +2 }}">Kelas : {{$data_kelas->nama_kelas}}</th>
				<th colspan="4" style="text-align:center">keterangan</th>
			</tr>

			<tr>

				<th style="width:min-content">No</th>
				<th>Nama</th>
				@foreach($tanggal_presensi as $t)
				@php
				$tanggal = Date('d',strtotime($t));
				$hari = Date('D',strtotime($t));
				$libur = '';

				if($hari == 'Sun'){
				@endphp
				<th style="font-size:9px; background-color:orangered; width:min-content">{{$tanggal}}</th>

				@php } else {
				@endphp
				<th style="font-size:9px; width:min-content">{{$tanggal}}</th>
				@php }
				@endphp
				@endforeach
				<th style="width:min-content">hadir</th>
				<th style="width:min-content">sakit</th>
				<th style="width:min-content">izin</th>
				<th style="width:min-content">alpha</th>
				<!-- <th>Email</th>
				<th>Alamat</th>
				<th>Telepon</th>
				<th>Pekerjaan</th> -->
			</tr>

		</thead>
		<tbody>
			@php $i=1;
			$hadir = 0;
			@endphp
			@foreach($daftar_presensi as $p)
			@php
			$jumlah_hadir = 0;
			$jumlah_sakit = 0;
			$jumlah_izin = 0;
			$jumlah_alpha = 0;
			@endphp
			<tr>
				<td>{{ $i++ }}</td>
				<td>{{$p->nama_siswa}}</td>
				@foreach($tanggal_presensi as $t)
				@php
				$tanggal = Date('d',strtotime($t));
				$hari = Date('D',strtotime($t));
				$tanggal_lengkap = Date('Y-m-d', strtotime($t));
				$libur = '';
				@endphp
				@if($hari == 'Sun')
				<td style="font-size:9px; background-color:orangered; width:min-content"></td>
				@else
				@if($presensi_perbulan[$p->user_id][$tanggal_lengkap] == 'h')
				@php $jumlah_hadir ++ @endphp
				<td style="font-size:9px; background-color:lightgreen; width:min-content; text-align:center">{{$presensi_perbulan[$p->user_id][$tanggal_lengkap]}} </td>
				@elseif($presensi_perbulan[$p->user_id][$tanggal_lengkap] == 's')
				@php $jumlah_sakit ++ @endphp
				<td style="font-size:9px; background-color:orange; width:min-content; text-align:center">{{$presensi_perbulan[$p->user_id][$tanggal_lengkap]}} </td>
				@elseif($presensi_perbulan[$p->user_id][$tanggal_lengkap] == 'i')
				@php $jumlah_izin ++ @endphp
				<td style="font-size:9px; background-color:orange; width:min-content; text-align:center">{{$presensi_perbulan[$p->user_id][$tanggal_lengkap]}} </td>
				@elseif($presensi_perbulan[$p->user_id][$tanggal_lengkap] == 'a')
				@php $jumlah_alpha ++ @endphp
				<td style="font-size:9px; background-color:red; width:min-content; text-align:center">{{$presensi_perbulan[$p->user_id][$tanggal_lengkap]}} </td>
				@else
				<td style="font-size:9px; width:min-content; text-align:center">{{$presensi_perbulan[$p->user_id][$tanggal_lengkap]}} </td>
				@endif
				@endif

				@endforeach
				<td style="text-align:center;">{{$jumlah_hadir}}</td>
				<td style="text-align:center;">{{$jumlah_sakit}}</td>
				<td style="text-align:center;">{{$jumlah_izin}}</td>
				<td style="text-align:center;">{{$jumlah_alpha}}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	&nbsp;
	<h6 style="text-align:right; font-style: TimesNewRoman"> Martapura , {{Date('d F Y')}}</h6>
	<h6 style="text-align:right; font-style: TimesNewRoman"> Guru Wali Kelas {{$data_kelas->nama_kelas}} &emsp;</h6>
	&nbsp;
	&nbsp;
	&nbsp;
	&nbsp;
	&nbsp;
	&nbsp;
	&nbsp;
	<h6 style="text-align:right; font-style: TimesNewRoman">{{$data_kelas->guru->name}}</h6>
</body>

</html>