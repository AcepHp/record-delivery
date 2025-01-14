@extends('layouts.app')

@section('header')
    <h2 class="text-3xl font-semibold text-gray-800">
        {{ __('Form Input') }}
    </h2>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <h1>Input Harga Part</h1>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="dataForm" autocomplete="off">
                            @csrf
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

                            <div class="mb-3 row">
                                <div class="col-md-8">
                                    <label for="harga" class="form-label">Harga</label>
                                    <input type="number" class="form-control @error('harga') is-invalid @enderror" id="harga" name="harga" value="{{ old('harga') }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="mata_uang" class="form-label">Tipe</label>
                                    <select class="form-select @error('mata_uang') is-invalid @enderror" id="mata_uang" name="mata_uang">
                                        <option value="" disabled selected>Mata Uang</option>
                                        @foreach($mata_uangs as $mata_uang)
                                            <option value="{{ $mata_uang }}" {{ old('mata_uang') == $mata_uang ? 'selected' : '' }}>
                                                {{ $mata_uang }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('mata_uang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="uom" class="form-label">Unit Of Measure</label>
                                    <select class="form-select @error('uom') is-invalid @enderror" id="uom" name="uom">
                                        <option value="" disabled selected>Select UOM</option>
                                        @foreach($uoms as $uom)
                                            <option value="{{ $uom }}" {{ old('uom') == $uom ? 'selected' : '' }}>
                                                {{ $uom }}
                                            </option>
                                        @endforeach
                                    </select>
                                @error('uom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="vendor" class="form-label">Vendor</label>
                                <input type="text" class="form-control @error('vendor') is-invalid @enderror" id="vendor" name="vendor" value="{{ old('vendor') }}">
                                @error('vendor')
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

<script>
    // Ketika form disubmit
    $('#dataForm').on('submit', function(e) {
        e.preventDefault();

        // Ambil data form
        var formData = $(this).serialize();

        // Kirim data dengan AJAX
        $.ajax({
            url: '{{ route('harga.store') }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        confirmButtonText: 'OK',
                        showConfirmButton: true,
                        timer: 4000
                    });
                    // Reset form setelah sukses
                    $('#dataForm')[0].reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message,
                        confirmButtonText: 'OK',
                        showConfirmButton: true,
                        timer: 4000
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan saat mengirim data.',
                    confirmButtonText: 'OK',
                    showConfirmButton: true,
                    timer: 4000
                });
            }
        });
    });

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




@endsection
