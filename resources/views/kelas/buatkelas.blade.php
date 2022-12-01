@extends('layout')

<head>
  <title>
    Tambah Kelas
  </title>
</head>



@section('dashboard-content')


<!-- End Navbar -->
<div class="panel-header panel-header-sm">
</div>
@if(Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <strong>{{ Session::get('success')}}</strong> mantap.
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
          <h5 class="title">Buat Kelas Baru</h5>
        </div>
        <div class="card-body">
          <form action="{{URL::to('simpan-kelas')}}" method="POST">
            @csrf
            <div class="row">
              <div class="col-md-6 pr-1">
                <div class="form-group">
                  <label>Nama Kelas</label>
                  <input type="text" class="form-control" placeholder="Ex : Kelas XI IPA 2" value="" name="nama_kelas">
                </div>
              </div>
              <div class="col-md-6 pl-1">
                <div class="form-group">
                  <label>Id Wali</label>
                  <!-- <input type="text" class="form-control" placeholder="Ex : Alwan Zaki,S.Kom" value="" name="id_guru"> -->
                  <select name="id_guru" class="form-control">
                    @foreach($User as $guru)
                    <option value="{{$guru->id}}">{{$guru->id}} - {{$guru->name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="Detail">Detail Kelas</label>
                  <textarea class="form-control" id="editor1" name="detail_kelas"> </textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12" style="text-align:center">
                <div class="form-group">
                  <button type="submit" class="btn btn-primary">Buat Kelas</button>
                </div>
              </div>
            </div>

          </form>
        </div>
      </div>
    </div>
    <!-- <div class="col-md-4">
      <div class="card card-user">
        <div class="image">
          <img src="../assets/img/bg5.jpg" alt="...">
        </div>
        <div class="card-body">
          <div class="author">
            <a href="#">
              <img class="avatar border-gray" src="../assets/img/mike.jpg" alt="...">
              <h5 class="title">Mike Andrew</h5>
            </a>
            <p class="description">
              michael24
            </p>
          </div>
          <p class="description text-center">
            "Lamborghini Mercy <br>
            Your chick she so thirsty <br>
            I'm in that two seat Lambo"
          </p>
        </div>
        <hr>
        <div class="button-container">
          <button href="#" class="btn btn-neutral btn-icon btn-round btn-lg">
            <i class="fab fa-facebook-f"></i>
          </button>
          <button href="#" class="btn btn-neutral btn-icon btn-round btn-lg">
            <i class="fab fa-twitter"></i>
          </button>
          <button href="#" class="btn btn-neutral btn-icon btn-round btn-lg">
            <i class="fab fa-google-plus-g"></i>
          </button>
        </div>
      </div>
    </div> -->
  </div>
</div>
<!-- <footer class="footer">
  <div class=" container-fluid ">
    <nav>
      <ul>
        <li>
          <a href="https://www.creative-tim.com">
            Creative Tim
          </a>
        </li>
        <li>
          <a href="http://presentation.creative-tim.com">
            About Us
          </a>
        </li>
        <li>
          <a href="http://blog.creative-tim.com">
            Blog
          </a>
        </li>
      </ul>
    </nav>
    <div class="copyright" id="copyright">
      &copy; <script>
        document.getElementById('copyright').appendChild(document.createTextNode(new Date().getFullYear()))
      </script>, Designed by <a href="https://www.invisionapp.com" target="_blank">Invision</a>. Coded by <a href="https://www.creative-tim.com" target="_blank">Creative Tim</a>.
    </div>
  </div>
</footer> -->
<!--   Core JS Files   -->



@stop