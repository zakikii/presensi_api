@extends('layout')

<head>
  <title>
    Daftar Kelas
  </title>
</head>
<script>
  function checkDelete() {
    var check = confirm('Are you sure you want to Delete this?');
    if (check) {
      return true;
    }
    return false;
  }
</script>

@section('dashboard-content')
<div class="panel-header panel-header-sm">
</div>
@if(Session::get('deleted'))
<div class="alert alert-danger alert-dismissible fade show" role="alert" id="gone">
  <strong> {{ Session::get('deleted') }} </strong>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif

@if(Session::get('delete-failed'))
<div class="alert alert-warning alert-dismissible fade show" role="alert" id="gone">
  <strong> {{ Session::get('delete-failed') }} </strong>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif

@if(Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" id="gone">
  <strong> {{ Session::get('success') }} </strong>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;
    </span>
  </button>
</div>
@endif

@if(Session::get('opened'))
<div class="alert alert-success alert-dismissible fade show" role="alert" id="gone">
  <!-- <strong class="mercado-countdown"> {{ Session::get('opened') }} -->
  <span id="demo"> </span>
  <!-- </strong> -->
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;

    </span>
  </button>
</div>
@endif

@if(Session::get('failed'))
<div class="alert alert-danger alert-dismissible fade show" role="alert" id="gone">
  <strong> {{ Session::get('failed') }} </strong>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif

<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title"> Daftar kelas</h4>
        </div>
        <div class="text-right">
          <a href="{{ URL::to('buat-kelas') }}" class="btn btn-primary "> Buat Kelas </a></t>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table">
              <thead class=" text-primary">
                <tr style="font-size:14px">
                  <th>
                    Kelas
                  </th>
                  <th>
                    Id Guru
                  </th>
                  <th>
                    Jumlah Siswa
                  </th>
                  <th>
                    Detail
                  </th>
                  <th>
                    Status
                  </th>
                  <th>
                    Kode
                  </th>
                  <th>
                    Aksi
                  </th>
                  <th>rekap</th>
              </thead>
              </tr>
              <tbody>
                @foreach($kelas as $kel)
                <tr style="font-size:13px">
                  <td>
                    {{$kel->nama_kelas}}
                  </td>
                  <td>
                    {{$kel->id_guru}}
                  </td>
                  <td>
                    {{$kel->jumlah_siswa}}
                  </td>
                  <td>
                    {!!$kel->detail_kelas!!}
                  </td>
                  <td>
                    <?php

                    if ($kel->status == 1) {
                      echo "open";
                    } else {
                      echo "close";
                    }
                    ?>
                    <!-- {{$kel->status}} -->
                  </td>
                  <td>
                    {{$kel->kode_kelas}}
                  </td>
                  <td>
                    <div>
                      <?php if ($kel->status == 0) { ?>
                        <!-- <a href="{{ URL::to('close-kelas') }}/{{ $kel->id }}" class="btn btn-outline-primary btn-sm"> Tutuptututup </a> -->
                        <form action="{{ URL::to('open-kelas') }}/{{ $kel->id }}" method="POST">
                          @csrf
                          <select style="display:inline-flex" name="menit" class="btn btn-outline-primary btn-sm" onchange="this.form.submit()">
                            <option value="" disabled selected>Buka</option>
                            <option value='15'>15 menit</option>
                            <option value='30'>30 menit</option>
                            <option value='45'>45 menit</option>
                            <option value='60'>1 jam</option>
                          </select>
                        </form>
                      <?php } else { ?>
                        <a href="{{ URL::to('close-kelas') }}/{{ $kel->id }}" class="btn btn-outline-primary btn-sm"> Tutup </a> <?php } ?> |
                      <a href="{{ URL::to('edit-kelas') }}/{{ $kel->id }}" class="btn btn-outline-primary btn-sm"> Edit </a>
                      | <a href="{{ URL::to('delete-kelas') }}/{{ $kel->id }}" class="btn btn-outline-danger btn-sm" onclick="checkDelete()"> Hapus</a>
                    </div>
                  </td>
                  <td>

                    <div class="form-group">

                      <form action="{{URL::to('download-rekap/bulan')}}" method="POST">
                        @csrf
                        <input type="hidden" value="{{$kel->id}}" name="id_kelas">
                        <select name="kategori" class="btn btn-outline-primary btn-sm" onchange="this.form.submit()">
                          <option value="" disabled selected>Rekap</option>
                          <option value=1>Januari</option>
                          <option value=2>Februari</option>
                          <option value=3>Maret</option>
                          <option value=4>April</option>
                          <option value=5>Mei</option>
                          <option value=6>Juni</option>
                          <option value=7>Juli</option>
                          <option value=8>Agustus</option>
                          <option value=9>September</option>
                          <option value=10>Oktober</option>
                          <option value=11>November</option>
                          <option value=12>Desember</option>
                        </select>

                      </form>
                    </div>
          </div>
          </td>
          <td>

            <div class="form-group">


              <form action="{{URL::to('download-rekap/semester')}}" method="POST">
                @csrf
                <input type="hidden" value="{{$kel->id}}" name="id_kelas" style="width:min-content">
                <select name="kategori" class="btn btn-outline-primary btn-sm" onchange="this.form.submit()">
                  <option value="" disabled selected>Rekap</option>

                  <option value="ganjil {{$tahunganjil}} {{$tahungenap}}">ganjil {{$tahunganjil}}/{{$tahungenap}}
                  </option>
                  <option value="genap {{$tahunganjil}} {{$tahungenap}}">genap {{$tahunganjil}}/{{$tahungenap}}
                  </option>
                  <option value="ganjil {{$tahunganjil-1}} {{$tahungenap-1}}">ganjil {{$tahunganjil-1}}/{{$tahungenap-1}}
                  </option>
                  <option value="genap {{$tahunganjil-1}} {{$tahungenap-1}}">genap {{$tahunganjil-1}}/{{$tahungenap-1}}
                  </option>
                </select>

                <!-- <input type="hidden" value="{{$tahunganjil}}" name="tahun_ganjil">
                <input type="hidden" value="{{$tahungenap}}" name="tahun_genap"> -->
              </form>
            </div>

          </td>

          </tr>
          @endforeach
          </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>
</div>


@php $t = $kelas[0]->close_time; @endphp
<script>
  var count_id = "<?php echo $t ?>"
  var countDownDate = new Date(count_id).getTime();
  var x = setInterval(function() {

    var now = new Date().getTime();

    // Find the distance between now and the count down date
    var distance = countDownDate - now;

    // Time calculations for days, hours, minutes and seconds
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

    // Output the result in an element with id="demo"
    document.getElementById("demo").innerHTML = days + "d " + hours + "h " +
      minutes + "m " + seconds + "s ";

    // If the count down is over, write some text 
    if (distance < 0) {
      clearInterval(x);
      document.getElementById("demo").innerHTML = "EXPIRED";
    }
  }, 1000);
</script>

@stop