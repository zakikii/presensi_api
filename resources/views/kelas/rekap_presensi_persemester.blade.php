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
			<h6>Semester {{$kategori}}, Tahun Ajaran {{$tahun_ajaran}}</h6>
	</center>
	&nbsp;
	<table class='table table-bordered' style='table-layout: fixed'>
		<thead>
			<tr>
				<th colspan="30">Kelas : {{$data_kelas->nama_kelas}}</th>
			</tr>
			<tr>
				<th style="width: 2%; text-align:center" rowspan="2">No</th>
				<th style="width:7%" rowspan="2">Nama</th>
				@php
				$j = 6;
				if($kategori == 'ganjil'){
				$bulan = array('Juli','Agustus','September','Oktober','November','Desember');}
				else{
				$bulan = array('Januari','Februari','Maret','April','Mei','Juni');}

				for($i = 0; $i<$j; $i++){ @endphp <th style="width:min-content; text-align:center" colspan="4">{{$bulan[$i]}}</th>
					@php } @endphp
					<th style="width:min-content; text-align:center" colspan="4">total</th>
			</tr>
			<tr style="text-align:center">
				@php
				$j = 6;
				for($i = 0; $i<$j; $i++){ @endphp <th style="width:2%;">h</th>
					<th style="width:2%">s</th>
					<th style="width:2%">i</th>
					<th style="width:2%">a</th>@php } @endphp
					<th style="width:2%;  background-color:lightgreen">hadir</th>
					<th style="width:2%;  background-color:yellow">sakit</th>
					<th style="width:2%;  background-color:yellow">izin</th>
					<th style="width:2%;  background-color:red">alpha</th>
			</tr>
		</thead>

		<tbody>
			@php
			$i = 1;
			@endphp
			@foreach($daftar_siswa as $siswa)
			@php
			$h=0;
			$s=0;
			$z=0;
			$a=0;
			@endphp
			<tr>
				<td style="text-align:center; width: 0.1%">{{$i++}}</td>
				<td>{{$siswa->nama_siswa}}</td>
				@foreach($interval as $in)
				<td style="text-align:center; width:2%">{{$data_presensi[$siswa->user_id][$in]['h']}} </td>
				<td style="text-align:center; width:2%">{{$data_presensi[$siswa->user_id][$in]['s']}} </td>
				<td style="text-align:center; width:2%">{{$data_presensi[$siswa->user_id][$in]['i']}} </td>
				<td style="text-align:center; width:2%">{{$data_presensi[$siswa->user_id][$in]['a']}} </td>

				@if ($data_presensi[$siswa->user_id][$in]['h'] != 0)
				@php $h = $h+$data_presensi[$siswa->user_id][$in]['h']; @endphp
				@elseif ($data_presensi[$siswa->user_id][$in]['s'] != 0)
				@php $s = $s+$data_presensi[$siswa->user_id][$in]['s']; @endphp
				@elseif ($data_presensi[$siswa->user_id][$in]['i'] != 0)
				@php $i = $i+$data_presensi[$siswa->user_id][$in]['i']; @endphp
				@elseif ($data_presensi[$siswa->user_id][$in]['a'] != 0)
				@php $a = $a+$data_presensi[$siswa->user_id][$in]['a']; @endphp
				@endif
				@endforeach
				<td style="text-align:center; width:2%">{{$h}}</td>
				<td style="text-align:center; width:2%">{{$s}}</td>
				<td style="text-align:center; width:2%">{{$z}}</td>
				<td style="text-align:center; width:2%">{{$a}}</td>
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