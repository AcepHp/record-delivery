@extends('layouts.app')

@section('header')
    <h2 class="text-3xl font-semibold text-gray-800">
        {{ __('Form Input') }}
    </h2>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <h1>Input Record Delivery</h1>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('record.store') }}" method="POST" id="dataForm" autocomplete="off">
                            @csrf
                            <div class="mb-3">
                                <label for="tgl_bln_thn" class="form-label">Tanggal</label>
                                <input type="date" class="form-control @error('tgl_bln_thn') is-invalid @enderror" id="tgl_bln_thn" name="tgl_bln_thn" value="{{ old('tgl_bln_thn') }}">
                                @error('tgl_bln_thn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="plant_dest" class="form-label">Plant Destination</label>
                                <input type="text" class="form-control @error('plant_dest') is-invalid @enderror" id="plant_dest" name="plant_dest" value="{{ old('plant_dest') }}" readonly>
                                @error('plant_dest')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="model" class="form-label">Model</label>
                                <select class="form-select @error('model') is-invalid @enderror" id="model" name="model">
                                        <option value="" disabled selected>Select Model</option>
                                        @foreach($modelss as $model)
                                            <option value="{{ $model }}" {{ old('model') == $model ? 'selected' : '' }}>
                                                {{ $model }}
                                            </option>
                                        @endforeach
                                    </select>
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="qty" class="form-label">Quantity</label>
                                <input type="number" class="form-control @error('qty') is-invalid @enderror" id="qty" name="qty" value="{{ old('qty') }}">
                                @error('qty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="pic" class="form-label">PIC</label>
                                <input type="text" class="form-control @error('pic') is-invalid @enderror" id="pic" name="pic" value="{{ old('pic') }}" readonly>
                                @error('pic')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-actions">
                                <button type="button" onclick="resetForm()" class="btn btn-outline-danger">Reset</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                        <div id="toast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" style="position: fixed; top: 10px; right: 10px; display: none;">
                            <div class="d-flex">
                                <div class="toast-body" id="toastMessage"></div>
                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function resetForm() {
        var form = document.getElementById('dataForm');
        form.reset();
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('model').addEventListener('change', function() {
            let model = this.value;
            
            if (model) {
                fetch(`/get-model-data/${model}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            document.getElementById('plant_dest').value = data.plant_dest;
                            document.getElementById('pic').value = data.pic;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    }
                );
            }
        });
    });
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
        width: 96.5%; 
        max-height: 230px;
        overflow-y: hidden;
        overflow-x: hidden;
    }

    .search-point:hover {
        overflow-y: auto;
    }
</style>


@endsection
