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
            <h1>Input Komponen Mesin</h1>
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
                        <form action="{{ route('data.store') }}" method="POST" id="dataForm" autocomplete="off">
                            @csrf
                                <div class="mb-3">
                                    <label for="line" class="form-label">Line</label>
                                    <select class="form-select @error('line') is-invalid @enderror" id="line" name="line">
                                        <option value="" disabled {{ old('line', session('line')) ? '' : 'selected' }}>Select Line</option>
                                        @foreach($lines as $line)
                                            <option value="{{ $line }}" {{ old('line', session('line')) == $line ? 'selected' : '' }}>
                                                {{ $line }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('line')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="no_station" class="form-label">No Station</label>
                                    <select class="form-select @error('no_station') is-invalid @enderror" id="no_station" name="no_station">
                                        <option value="" disabled {{ old('no_station', session('no_station')) ? '' : 'selected' }}>Select No Station</option>
                                        @foreach($noStations as $noStation)
                                            <option value="{{ $noStation }}" {{ old('no_station', session('no_station')) == $noStation ? 'selected' : '' }}>
                                                {{ $noStation }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('no_station')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="nama_station" class="form-label">Nama Station</label>
                                    <select class="form-select @error('nama_station') is-invalid @enderror" id="nama_station" name="nama_station">
                                        <option value="" disabled {{ old('nama_station', session('nama_station')) ? '' : 'selected' }}>Select Nama Station</option>
                                        @foreach($namaStations as $namaStation)
                                            <option value="{{ $namaStation }}" {{ old('nama_station', session('nama_station')) == $namaStation ? 'selected' : '' }}>
                                                {{ $namaStation }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('nama_station')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            
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
                                    <input type="text" class="form-control @error('kode_barang') is-invalid @enderror" id="kode_barang" name="kode_barang" value="{{ old('kode_barang') }}" readonly style="background-color: light-gray">
                                    @error('kode_barang')
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

@section('style')
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
@endsection

@section('scripts')
<script>
function resetForm() {
    var form = document.getElementById('dataForm');
    form.reset();

    var lineSelect = document.getElementById('line');
    var noStationSelect = document.getElementById('no_station');
    var namaStationSelect = document.getElementById('nama_station');

    
    noStationSelect.innerHTML = '<option value="" disabled selected>Select No Station</option>';
    namaStationSelect.innerHTML = '<option value="" disabled selected>Select Nama Station</option>';

    // Mengambil data line dan mengisi dropdown
    fetch('/data/lines')
        .then(response => response.json())
        .then(data => {
            lineSelect.innerHTML = '<option value="" disabled selected>Select Line</option>';
            data.lines.forEach(function(line) {
                var option = document.createElement('option');
                option.value = line;
                option.text = line;
                lineSelect.add(option);
            });
        })
        .catch(error => console.error('Error fetching lines:', error));
}

document.getElementById('line').addEventListener('change', function() {
    var line = this.value;
    var noStationSelect = document.getElementById('no_station');
    var namaStationSelect = document.getElementById('nama_station');

    noStationSelect.innerHTML = '<option value="" disabled selected>Select No Station</option>';
    namaStationSelect.innerHTML = '<option value="" disabled selected>Select Nama Station</option>';

    if (line) {
        if (line.includes('SMT')) {
            noStationSelect.disabled = true;
            fetch(`/data/nama-stations/${encodeURIComponent(line)}`)
                .then(response => response.json())
                .then(data => {
                    data.nama_stations.forEach(function(nama_station) {
                        var option = document.createElement('option');
                        option.value = nama_station;
                        option.text = nama_station;
                        namaStationSelect.add(option);
                    });
                })
                .catch(error => console.error('Error fetching nama stations:', error));
        } else {
            noStationSelect.disabled = false;
            fetch(`/data/no-stations/${encodeURIComponent(line)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.no_stations.length === 0) {
                        noStationSelect.innerHTML = '<option value="" disabled>No No Station Available</option>';
                    } else {
                        data.no_stations.forEach(function(no_station) {
                            var option = document.createElement('option');
                            option.value = no_station;
                            option.text = no_station;
                            noStationSelect.add(option);
                        });
                    }
                })
                .catch(error => console.error('Error fetching no stations:', error));
        }
    }
});

// Add event listener for no_station change
document.getElementById('no_station').addEventListener('change', function() {
    var noStation = this.value;
    var namaStationSelect = document.getElementById('nama_station');

    namaStationSelect.innerHTML = '<option value="" disabled selected>Select Nama Station</option>';

    if (noStation) {
        fetch(`/data/nama-stations/${encodeURIComponent(noStation)}`)
            .then(response => response.json())
            .then(data => {
                data.nama_stations.forEach(function(nama_station) {
                    var option = document.createElement('option');
                    option.value = nama_station;
                    option.text = nama_station;
                    namaStationSelect.add(option);
                });
            })
            .catch(error => console.error('Error fetching nama stations:', error));
    }
});
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

</body>
@endsection
