@extends('layouts.app')

@section('header')
    <h2 class="text-3xl font-semibold text-gray-800">
        {{ __('Form Input Baru') }}
    </h2>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-flex-start align-items-center">
            <h1>Komponen Mesin Baru</h1>
            <div class="dropdown">
                <button class="btn btn-secondary btn-smsa dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-caret-down"></i>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item" href="{{ route('data.create') }}">Input Komponen</a></li>
                    <li><a class="dropdown-item" href="{{ route('data.new.create') }}">Input Komponen Baru</a></li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('data.new.store') }}" method="POST" id="dataForm" autocomplete="off">
                            @csrf
                                <div class="mb-3">
                                    <label for="nama_barang" class="form-label">Nama Barang</label>
                                    <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" id="nama_barang" name="nama_barang" value="{{ old('nama_barang') }}">
                                    @error('nama_barang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="kode_barang" class="form-label">Kode Barang</label>
                                    <input type="text" class="form-control @error('kode_barang') is-invalid @enderror" id="kode_barang" name="kode_barang" value="{{ old('kode_barang') }}" style="background-color: light-gray">
                                    @error('kode_barang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            <div class="form-actions">
                                <button type="button" onclick="resetForm()" class="btn btn-outline-danger">Reset</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: "{{ session('success') }}",
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: "{{ session('error') }}",
    });
</script>
@endif

<style>
    .form-actions {
        display: flex;
        justify-content: space-between; 
        margin-top: 20px; 
    }

    .search-point {
        position: absolute; 
        z-index: 1000; 
        background: white; 
        width: 95%; 
        max-height: 230px;
        overflow-y: hidden;
        overflow-x: hidden;
    }

    .search-point:hover {
        overflow-y: auto;
    }
    .btn-smsa {
        background-color: #f4f5f8;
        color: #000;
    }

    .btn-smsa:focus,
    .btn-smsa:hover,
    .btn-smsa:active {
        background-color: #f4f5f8;
        border-color: #f4f5f8;
        color: #000; 
        box-shadow: none;
    }
</style>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function resetForm() {
        var form = document.getElementById('dataForm');
        form.reset();
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK',
                showConfirmButton: true,
                timer: 4000 
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
                confirmButtonText: 'OK',
                showConfirmButton: true,
                timer: 4000 
            });
        @endif
    });
</script>

</body>
@endsection
