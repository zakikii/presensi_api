@extends('layout')

<head>
  <title>
    Daftar Slider
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
<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title"> Daftar SLider</h4>
        </div>
        <div class="text-right">
          <a href="{{ URL::to('buat-slider') }}" class="btn btn-primary "> Buat Slider </a></t>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table">
              <thead class=" text-primary">
                <th>
                  Judul Slider
                </th>
                <th>
                  Detail Slider
                </th>
                <th>
                  Gambar Slider
                </th>
                <th>
                  Aksi
                </th>
              </thead>
              <tbody>
                @foreach($slider as $slide)
                <tr>
                  <td>
                    {{$slide->judul_slider}}
                  </td>
                  <td>
                    {{$slide->detail_slider}}
                  </td>
                  <td> <img src="{{ $slide->gambar_slider }}" width="100" height="100"></td>
                  <td>
                    <a href="{{ URL::to('edit-slider') }}/{{ $slide->id }}" class="btn btn-outline-primary btn-sm"> Edit </a>
                    |
                    <a href="{{ URL::to('delete-slider') }}/{{ $slide->id }}" class="btn btn-outline-danger btn-sm" onclick="checkDelete()"> Delete </a>
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

@stop