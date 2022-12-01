@extends('layout')

<head>
    <title>
        Edit Data Kelas
    </title>
</head>


@section('dashboard-content')
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

<!-- End Navbar -->
<div class="panel-header panel-header-sm">
</div>
<div class="content">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h5 class="title">Edit data kelas</h5>
                </div>
                <div class="card-body">
                    <form action="{{URL::to('update-kelas')}}/{{$kelas->id}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 pr-1">
                                <div class="form-group">
                                    <label>Nama Kelas</label>
                                    <input type="text" class="form-control" placeholder="Ex : Kelas XI IPA 2" value="{{$kelas->nama_kelas}}" name="nama_kelas">
                                </div>
                            </div>
                            <div class="col-md-6 pl-1">
                                <div class="form-group">
                                    <label>Id Guru</label>
                                    <input type="text" class="form-control" placeholder="Ex : Alwan Zaki,S.Kom" value="{{$kelas->id_guru}}" name="id_guru">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="Detail">Detail Kelas</label>
                                    <textarea class="form-control" id="editor1" name="detail_kelas">{{$kelas->detail_kelas}} </textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" style="text-align:center">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Edit Kelas</button>
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