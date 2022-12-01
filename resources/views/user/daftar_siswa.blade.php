@extends('layout')


<head>
    <title>
        Daftar Siswa
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
        <span aria-hidden="true">&times;</span>
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
                    <h4 class="card-title">Daftar Siswa</h4>
                </div>
                <!-- <div class="text-right">
                    <a href="{{ URL::to('buat-kelas') }}" class="btn btn-primary "> Buat Kelas </a></t>
                </div> -->

                <div class="card-body">
                    <div class="table-responsive">
                        @if(isset($siswa))
                        <table class="table" id="datatablesSimple">
                            <thead class=" text-primary">

                                <th>
                                    Id
                                </th>
                                <th>
                                    Nama Siswa
                                </th>
                                <th>
                                    Kelas
                                </th>

                            </thead>
                            <tbody>

                                @foreach($siswa as $user)



                                <tr>


                                    <td>
                                        {{$user->id}}
                                    </td>
                                    <td>
                                        {{$user->name}}
                                    </td>
                                    <td>
                                        {{$user->kelas->nama_kelas}}
                                    </td>


                                </tr>

                                @endforeach

                            </tbody>

                        </table>
                        {!! $siswa->render() !!}@endif
                    </div>

                </div>


            </div>
        </div>

    </div>
</div>

@stop