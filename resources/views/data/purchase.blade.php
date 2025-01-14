@extends('layouts.app')

@section('header')
    <h2 class="text-3xl font-semibold text-gray-800">
        {{ __('Form Input') }}
    </h2>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-flex-start align-items-center">
            <h1>Purchase Request</h1>
            <div class="dropdown">
                <button class="btn btn-secondary btn-smsa dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-caret-down"></i>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item" href="{{ route('purchase.create') }}">Form Input</a></li>
                    <li><a class="dropdown-item" href="{{ route('purchase.new.create') }}">Tabel Purchase Request</a></li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('purchase.store') }}" method="POST" id="dataForm" autocomplete="off">
                            @csrf
                                <div class="mb-3">
                                    <label for="ref_pp" class="form-label">PR Number</label>
                                    <input type="text" class="form-control @error('ref_pp') is-invalid @enderror" id="ref_pp" name="ref_pp" value="{{ old('ref_pp') }}">
                                    @error('ref_pp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="req_date" class="form-label">Request Date</label>
                                    <input type="date" class="form-control @error('req_date') is-invalid @enderror" id="req_date" name="req_date" value="{{ old('req_date') }}">
                                    @error('req_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div id="dynamicItemContainer">
                                <div class="item-row">
                                    <div class="mb-3">
                                        <label for="nama_barang" class="form-label">Nama Barang</label>
                                        <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" id="nama_barang" name="nama_barang[]" value="{{ old('nama_barang') }}">
                                        @error('nama_barang')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class ="search-point" id="search_results"></div>
                                    <div class="form-actions"></div>
                                    <div class="mb-3">
                                        <label for="kode_barang" class="form-label">Kode Barang</label>
                                        <input type="text" class="form-control @error('kode_barang') is-invalid @enderror" id="kode_barang" name="kode_barang[]" readonly style="background-color: light-gray">
                                        @error('kode_barang')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="qty_pr" class="form-label">Quantity</label>
                                        <input type="number" class="form-control @error('qty_pr') is-invalid @enderror" id="qty_pr" name="qty_pr[]" value="{{ old('qty_pr') }}">
                                        @error('qty_pr')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="button" class="btn btn-danger remove-item">Remove</button>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="button" onclick="resetForm()" class="btn btn-outline-danger">Reset</button>
                                <button type="button" class="btn btn-success" id="addItemButton">Add Item</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
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

<script>
function resetForm() {
    var form = document.getElementById('dataForm');
    form.reset();
    }
</script>

<script>
document.getElementById('dataForm').addEventListener('submit', function(event) {
    var refPP = document.getElementById('ref_pp').value;
    var reqDate = document.getElementById('req_date').value;
    var itemRows = document.querySelectorAll('.item-row');
    var allItemsFilled = true;

    if (!refPP || !reqDate) {
        allItemsFilled = false;
    }

    itemRows.forEach(function(row) {
        var namaBarang = row.querySelector('input[name="nama_barang[]"]').value;
        var qtyPR = row.querySelector('input[name="qty_pr[]"]').value;
        if (!namaBarang || !qtyPR) {
            allItemsFilled = false;
        }
    });

    if (!allItemsFilled) {
        event.preventDefault(); 
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Harap isi semua kolom terlebih dahulu!',
            confirmButtonText: 'OK'
        });
    }
});

document.getElementById('addItemButton').addEventListener('click', function() {
    var itemContainer = document.getElementById('dynamicItemContainer');
    var newItemRow = document.querySelector('.item-row').cloneNode(true);

    newItemRow.querySelector('input[name="nama_barang[]"]').value = '';
    newItemRow.querySelector('input[name="kode_barang[]"]').value = '';
    newItemRow.querySelector('input[name="qty_pr[]"]').value = '';

    itemContainer.appendChild(newItemRow);

    attachRemoveEvent();
    attachSearchEvent(newItemRow.querySelector('input[name="nama_barang[]"]'));
});

function attachRemoveEvent() {
    document.querySelectorAll('.remove-item').forEach(function(button) {
        button.addEventListener('click', function() {
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data input ini akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, Hapus data!',
                cancelButtonText: 'Tidak, Simpan data kembali'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.closest('.item-row').remove();
                }
            });
        });
    });
}

function attachSearchEvent(inputElement) {
    inputElement.addEventListener('input', function() {
        var namaBarang = this.value;
        var searchResults = this.closest('.item-row').querySelector('.search-point');

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
                                inputElement.value = item.nama_barang;
                                inputElement.closest('.item-row').querySelector('input[name="kode_barang[]"]').value = item.kode_barang;
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
}

attachRemoveEvent();
document.querySelectorAll('input[name="nama_barang[]"]').forEach(input => {
    attachSearchEvent(input);
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

</body>
@endsection
