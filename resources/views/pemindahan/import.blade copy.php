@extends('layouts.app')

@section('header')
    <h2 class="text-3xl font-semibold text-gray-800">
        {{ __('Import') }}
    </h2>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <h1>Import Excel</h1>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('spareparts.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="file">Select Excel File</label>
                                <input type="file" name="file" id="file" class="form-control">
                                @error('file')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary btn-rs">Import</button>
                        </form>
                        @if (session('success'))
                            <div class="alert alert-success mt-2">{{ session('success') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tabel</h4>
                    <table id="spareparts-table" class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Barang</th>
                                <th>Kode Barang</th>
                                <th>Address</th>
                                <th>Total Qty</th>
                                <th>Lifetime (W)</th>
                                <th>Leadtime (W)</th>
                                <th>Minimal Stock</th>
                                <th>Stock Akhir Warehouse</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-rs {
        margin-top: 10px;
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#spareparts-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('spareparts.table') }}', 
            columns: [
                { data: 'nama_barang', name: 'nama_barang' }, 
                { data: 'kode_barang', name: 'kode_barang' },
                { data: 'address', name: 'address' },
                { data: 'total_qty', name: 'total_qty' },
                { data: 'leadtime', name: 'leadtime' },
                { data: 'lifetime', name: 'lifetime' },
                { data: 'min_stock', name: 'min_stock' },
                { data: 'stock_akhir_wrhs', name: 'stock_akhir_wrhs' },
            ],
            paging: true,
            searching: true,
            ordering: true,
            info: false,
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            lengthChange: false,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/English.json'
            }
        });
    });
</script>
@endsection
