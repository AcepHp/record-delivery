@extends('layouts.app')

@section('header')
    <h2 class="text-3xl font-semibold text-gray-800">
        {{ __('Form Input') }}
    </h2>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <h1>Input Stock Part</h1>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('stock.store') }}" method="POST" id="dataForm" autocomplete="off">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nama_barang" class="form-label">Nama Barang</label>
                                        <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" id="nama_barang" name="nama_barang" value="{{ old('nama_barang') }}">
                                        @error('nama_barang')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                        <div class ="search-point" id="search_results"></div>
                                        <div class="form-actions"></div>

                                    <div class="mb-3">
                                        <label for="kode_barang" class="form-label">Kode Barang</label>
                                        <input type="text" class="form-control @error('kode_barang') is-invalid @enderror" id="kode_barang" name="kode_barang" value="{{ old('kode_barang') }}" readonly>
                                        @error('kode_barang')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}">
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="lifetime" class="form-label">Lifetime (week)</label>
                                        <input type="number" class="form-control @error('lifetime') is-invalid @enderror" id="lifetime" name="lifetime" value="{{ old('lifetime') }}">
                                        @error('lifetime')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="leadtime" class="form-label">Leadtime (week)</label>
                                        <input type="number" class="form-control @error('leadtime') is-invalid @enderror" id="leadtime" name="leadtime" value="{{ old('leadtime') }}">
                                        @error('leadtime')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="stock_wrhs" class="form-label">Stock Warehouse</label>
                                        <input type="number" class="form-control @error('stock_wrhs') is-invalid @enderror" id="stock_wrhs" name="stock_wrhs" value="{{ old('stock_wrhs') }}">
                                        @error('stock_wrhs')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
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
document.getElementById('nama_barang').addEventListener('input', function() {
    var namaBarang = this.value;
    var searchResults = document.getElementById('search_results');
    
    if (namaBarang.length >= 2) {
        fetch(`/data/search-nama-barang/${encodeURIComponent(namaBarang)}`)
            .then(response => response.json())
            .then(data => {
                searchResults.innerHTML = '';
                if (data.length > 0) {
                    var list = document.createElement('ul');
                    list.classList.add('list-group');
                    data.forEach(item => {
                        var listItem = document.createElement('li');
                        listItem.classList.add('list-group-item');
                        listItem.textContent = `${item.nama_barang} - ${item.kode_barang}`; 
                        listItem.dataset.nama = item.nama_barang;
                        listItem.dataset.kode = item.kode_barang;
                        listItem.addEventListener('click', function() {
                            document.getElementById('nama_barang').value = item.nama_barang;
                            document.getElementById('kode_barang').value = item.kode_barang;
                            searchResults.innerHTML = '';
                        });
                        list.appendChild(listItem);
                    });
                    searchResults.appendChild(list);
                } else {
                    searchResults.innerHTML = '<p>No results found.</p>';
                }
            })
            .catch(error => console.error('Error:', error));
    } else {
        searchResults.innerHTML = '';
    }
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


@endsection
