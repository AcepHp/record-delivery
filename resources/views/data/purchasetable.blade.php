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
            <h1>Delivery Summary</h1>
            <div class="dropdown">
                <button class="btn btn-secondary btn-smsa dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-caret-down"></i>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item" href="{{ route('data.create') }}">Form Input</a></li>
                    <li><a class="dropdown-item" href="{{ route('data.new.create') }}">Tabel Purchase Request</a></li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tabel Persetujuan Purchase Request</h3>
                    </div>
                    <div class="card-body">
                        <table id="spareparts-table" class="table table-hover table-bordered table-responsive">
                            <thead class="table-header text-center"> 
                                <tr>
                                    <th>PR Number</th>
                                    <th>Request Date</th>
                                    <th>Nama Barang</th>
                                    <th>Kode Barang</th>
                                    <th>Qty</th>
                                    <th class="text-center">Action</th>
                                    <th>Status</th>
                                    <th>Approval Date</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tabel Pengiriman Barang</h3>
                    </div>
                    <div class="card-body">
                        <table id="spareparts-table-2" class="table table-hover table-bordered table-responsive">
                            <thead class="table-header text-center"> 
                                <tr>
                                    <th>PR Number</th>
                                    <th>Request Date</th>
                                    <th>Nama Barang</th>
                                    <th>Kode Barang</th>
                                    <th>Qty Received</th>
                                    <th>Received Date</th>
                                    <th>Sisa Pengiriman</th>
                                </tr>
                            </thead>
                            <tbody class="text-center"> 
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('style')
<style>
    .table {
        text-align: center; 
    }
    .table thead th, .table tbody td {
        vertical-align: middle;
    }

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
    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 5px;
    }
    .action-buttons button {
        min-width: 80px;
    }
    .table-header th {
        color: #fff; 
        background-color: #2088ef;
    }

    /* Mobile Devices */
    @media (max-width: 900px) {
        .table {
            font-size: 9.5px; 
        }
        .table thead th, .table tbody td {
            padding: 0.5rem;
        }
    }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#spareparts-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('purchase.new.data') }}',
            data: function (d) {
                d.status = $('#status-filter').val();
            }
        },
        columns: [
            { data: 'ref_pp', name: 'ref_pp' },
            { data: 'req_date', name: 'req_date' },
            { data: 'nama_barang', name: 'nama_barang' },
            { data: 'kode_barang', name: 'kode_barang' },
            { data: 'qty_pr', name: 'qty_pr' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'status_submit', name: 'status_submit' },
            { data: 'submit_date', name: 'submit_date', orderable: false, searchable: false },
        ],
        paging: true,
        searching: true,
        ordering: true,
        order: [[1, 'desc'], [0, 'asc']],
        info: false,
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50],
        lengthChange: false,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/English.json'
        }
    });

    $('#status-filter').change(function () {
        table.draw();
    });

    // Approve button
    $(document).on('click', '.approve-btn', function() {
        var id = $(this).data('id');
        var currentDate = new Date().toISOString().slice(0, 10);

        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Anda akan menyetujui permintaan berikut!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, saya setuju!'
        }).then((result) => {
            if (result.isConfirmed) {
                updateStatus(id, 'Approve', currentDate);
                Swal.fire(
                    'Approved!',
                    'Purchase Request sudah disetujui.',
                    'success'
                );
            }
        });
    });

    // Reject button
    $(document).on('click', '.reject-btn', function() {
        var id = $(this).data('id');
        var currentDate = new Date().toISOString().slice(0, 10);

        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Anda akan menolak permintaan berikut!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, saya menolak!'
        }).then((result) => {
            if (result.isConfirmed) {
                updateStatus(id, 'Reject', currentDate);
                Swal.fire(
                    'Rejected!',
                    'Purchase Request tidak disetujui.',
                    'success'
                );
            }
        });
    });

    function updateStatus(id, status, currentDate) {
        $.ajax({
            url: '{{ route('purchase.update.status') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                status: status,
                submit_date: currentDate 
            },
            success: function(response) {
                if (response.success) {
                    table.ajax.reload();
                } else {
                    alert('Error updating status.');
                }
            },
            error: function() {
                alert('An error occurred while updating the status.');
            }
        });
    }
});
</script>

<script>
    $(document).ready(function() {
        $('#spareparts-table-2').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('purchase.new.tabel') }}', 
            columns: [
                { data: 'ref_pp', name: 'ref_pp' },
                { data: 'req_date', name: 'req_date' },
                { data: 'nama_barang', name: 'nama_barang' },
                { data: 'kode_barang', name: 'kode_barang' },
                { data: 'qty', name: 'qty' },
                { data: 'received_date', name: 'tanggal' },
                { data: 'sisa_rcvid', name: 'sisa_rcvid' }
            ],
            paging: true,
            searching: true,
            ordering: true,
            order: [[1, 'desc'], [0, 'asc']],
            info: false,
            pageLength: 5,
            lengthMenu: [5, 10, 25, 50],
            lengthChange: false,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/English.json'
            }
        });
        
    });
</script>
@endsection

</body>
@endsection
