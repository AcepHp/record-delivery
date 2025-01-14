@extends('layouts.appusr')

@section('header')
    <h2 class="text-3xl font-semibold text-gray-800 dark:text-gray-200">
        {{ __('Dashboard') }}
    </h2>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Monitoring Sparepart</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Monitoring Sparepart</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->
        
        <div class="row">
            <div class="col-xl-4 col-md-6">
                <div class="card card-h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-muted mb-3 lh-1 d-block text-truncate"><b>Part Masuk & Keluar</b></span>
                                <h4 class="mb-3">
                                    <span class="counter-value" data-target="{{ $partMasuk + $partKeluar }}">0</span>
                                </h4>
                            </div>
                            <div class="flex-shrink-0 text-end dash-widget">
                                <div id="donut-chart1" class="apex-charts"></div>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div>

            <div class="col-xl-4 col-md-6">
                <div class="card card-h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-muted mb-3 lh-1 d-block text-truncate"><b>Status Barang</b></span>
                                <h4 class="mb-3">
                                    <span class="counter-value" data-target="{{ $statusOK + $statusDanger}}">0</span>
                                </h4>
                            </div>
                            <div class="flex-shrink-0 text-end dash-widget">
                                <div id="donut-chart3" class="apex-charts"></div>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div> 
        </div><!-- end row -->

        <div class="row mb-3">
            <div class="col-md-6">
                <form id="filter-form" class="form-inline">
                    <div class="form-group mr-3">
                        <label for="status-filter" class="mr-2">Filter :</label>
                        <select id="status-filter" class="form-control">
                            <option value="">All Status</option>
                            <option value="OK">OK</option>
                            <option value="DANGER">DANGER</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="col-md-6 text-right">
                <!-- Optionally, you can add additional buttons or content here -->
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table id="spareparts-table" class="table table-hover table-bordered table-responsive">
                            <thead class="table-header">
                                <tr>
                                    <th>Nama Barang</th>
                                    <th>Kode Barang</th>
                                    <th>Address</th>
                                    <th>Qty Dimesin</th>
                                    <th>Lifetime (week)</th>
                                    <th>Leadtime (week)</th>
                                    <th>Minimal Stock</th>
                                    <th>Stock Akhir Warehouse</th>
                                    <th>Status</th>
                                    <th>Add Data</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- end row-->                        
    </div>
    <!-- container-fluid -->
</div>
@endsection

@section('style')
<style>
    .table-rs thead {
        background-color: #b3d9ff; 
        color: white;
    }

    .table-rs thead th {
        text-align: center;
    }
    .table-header th {
        color: #fff; 
        background-color: #2088ef;
    }

</style>
@endsection

@section('scripts')
<script>
   $(document).ready(function() {
    var table = $('#spareparts-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true, // Keep responsive true
        ajax: {
            url: '{{ route('spareparts.data') }}',
            data: function (d) {
                d.status = $('#status-filter').val();
            }
        },
        columns: [
            { data: 'nama_barang', name: 'nama_barang', responsivePriority: 1 }, // Always priority
            { data: 'kode_barang', name: 'kode_barang', responsivePriority: 2 }, // Always priority
            { data: 'address', name: 'address', responsivePriority: 6 },         // Low priority (hidden di mobile)
            { data: 'total_qty', name: 'total_qty', responsivePriority: 5 },     // Low priority (hidden di mobile)
            { data: 'lifetime', name: 'lifetime', responsivePriority: 7 },       // Low priority (hidden di mobile)
            { data: 'leadtime', name: 'leadtime', responsivePriority: 8 },       // Low priority (hidden di mobile)
            { data: 'min_stock', name: 'min_stock', responsivePriority: 3 },     // Medium priority
            { data: 'stock_akhir_wrhs', name: 'stock_akhir_wrhs', responsivePriority: 4 }, // Medium priority
            { data: 'action', name: 'action', orderable: false, searchable: false, responsivePriority: 1 },
            {
                data: 'kode_barang',
                name: 'kode_barang',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return '<button class="btn btn-outline-primary custom-btn add-data" style="border-radius: 10px;" data-nama="' + row.nama_barang + '" data-kode="' + row.kode_barang + '">+</button>';
                },
                responsivePriority: 1 // Always priority
            }
        ],
        paging: true,
        searching: true,
        ordering: true,
        info: false,
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50],
        lengthChange: false
    });

    $('#status-filter').change(function () {
        table.draw();
    });

    $('#spareparts-table').on('click', '.add-data', function() {
        var namaBarang = $(this).data('nama');
        var kodeBarang = $(this).data('kode');
        window.location.href = '{{ route('partkeluaruser.create') }}?nama_barang=' + encodeURIComponent(namaBarang) + '&kode_barang=' + encodeURIComponent(kodeBarang);
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var options1 = {
        chart: {
            type: 'donut',
            height: 80,
            sparkline: {
                enabled: true
            }
        },
        series: [{{ $partKeluar }}, {{ $partMasuk }}],
        labels: ['Part Keluar', 'Part Masuk'],
        colors: ['#f5911f', '#007bff'],
        tooltip: {
            y: {
                formatter: function(val) {
                    return val;
                }
            }
        }
    };
    var chart1 = new ApexCharts(document.querySelector("#donut-chart1"), options1);
    chart1.render();

    var options3 = {
        chart: {
            type: 'donut',
            height: 80,
            sparkline: {
                enabled: true
            }
        },
        series: [{{ $statusDanger }}, {{ $statusOK }}],
        labels: ['Status Danger', 'Status OK'],
        colors: ['#dc3545', '#28a745'], 
        tooltip: {
            y: {
                formatter: function(val) {
                    return val;
                }
            }
        }
    };
    var chart3 = new ApexCharts(document.querySelector("#donut-chart3"), options3);
    chart3.render();
});
</script>

@endsection
