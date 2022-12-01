@extends('layout')

<head>
  <title>
    Edit Slider
  </title>
</head>



@section('dashboard-content')
<script>
  function loadPhoto(event) {
    var reader = new FileReader();
    reader.onload = function() {
      var output = document.getElementById('photo');
      output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
  }
</script>


<!-- End Navbar -->
<div class="panel-header panel-header-sm">
</div>
@if(Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <strong>{{ Session::get('success')}}</strong>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif

@if(Session::get('failed'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <strong>{{ Session::get('failed')}}</strong>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif
<div class="content">
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-header">
          <h5 class="title">Edit Slider</h5>
        </div>
        <div class="card-body">
          <form action="{{URL::to('update-slider')}}/{{$slider->id}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label>Judul Slider</label>
                  <input type="text" class="form-control" value="{{$slider->judul_slider}}" placeholder="Ex : siswa berprestasi 2021" name="judul_slider">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="Detail">Detail Slider</label>
                  <textarea class="form-control" id="editor1" name="detail_slider" value="{{$slider->detail_slider}}"> </textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label>Gambar Slider</label>
                  <img id="photo" height="100" width="100">
                  <input type="file" class="form-control" value="{{$slider->gambar_slider}}" name="gambar_slider" onchange="loadPhoto(event)">
                </div>
              </div>
            </div>


            <div class="row">
              <div class="col-md-12" style="text-align:center">
                <div class="form-group">
                  <button type="submit" class="btn btn-primary">Buat Slider</button>
                </div>
              </div>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>



@stop