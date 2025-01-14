@extends('layouts.app')

@section('header')
    <h2 class="text-3xl font-semibold text-gray-800">
        {{ __('Form Input') }}
    </h2>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <h1>Scan Record Delivery - {{ session('no_transaksi') }}</h1>
        <div class="table-responsive">
            <table class="table">
                <thead class="table-header">
                    <tr>
                        <th>Tanggal</th>
                        <th>Plant Destination</th>
                        <th>Model</th>
                        <th>PIC</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ session('tgl_bln_thn') }}</td>
                        <td>{{ session('plant_dest') }}</td>
                        <td>{{ session('model') }}</td>
                        <td>{{ session('pic') }}</td>
                        <td>{{ session('qty') }}</td>                       
                    </tr>
                </tbody>
            </table>
        </div>  
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('delivery.store') }}" method="POST" id="dataForm" autocomplete="off">
                            @csrf
                            <input type="hidden" name="no_transaksi" value="{{ $noTransaksi }}">
                            <div class="mb-3">
                                <label for="qrcode" class="form-label">Scan Data:</label>
                                <input type="text" class="form-control @error('qrcode') is-invalid @enderror" id="qrcode" name="qrcode" required autofocus readonly>
                                @error('qrcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div id="notification" style="display: none;" class="alert"></div>
                            <div class="form-actions">
                                <button type="button" onclick="resetForm()" class="btn btn-outline-danger">Reset</button>
                                <button type="button" class="btn btn-success" onclick="compareQty()">Finish</button>
                            </div>
                        </form>
                        <div id="record-table" style="display: none; margin-top: 10px">
                            <h3>Record Data</h3>
                            <table class="table table-hover">
                                <thead class="table-header">
                                    <tr>
                                        <th>No Transaksi</th>
                                        <th>Model</th>
                                        <th>Qty</th>
                                    </tr>
                                </thead>
                                <tbody id="record-data"></tbody>
                            </table>
                        </div>

                        <div id="delivery-table" style="display: none;">
                            <h3>Delivery Data</h3>
                            <table class="table table-hover">
                                <thead class="table-header">
                                    <tr>
                                        <th>No Transaksi</th>
                                        <th>Max Model</th>
                                        <th>Qty</th>
                                    </tr>
                                </thead>
                                <tbody id="delivery-data"></tbody>
                            </table>
                        </div>
                        <div id="toast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" style="position: fixed; top: 10px; right: 10px; display: none;">
                            <div class="d-flex">
                                <div class="toast-body" id="toastMessage"></div>
                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                        </div>
                        <h3 style="margin-top: 10px">Data Input</h3>
                        <table id="deliveryTable" class="table table-hover">
                            <thead class="table-header">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Model</th>
                                    <th>Lot Number</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- javascript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> 
    </div>
</div>

<script>
$(document).ready(function() {
    var table = $('#deliveryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('delivery.data') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'tgl_bln_thn', name: 'tgl_bln_thn' },
            { data: 'max_model', name: 'max_model' },
            { data: 'lot_number', name: 'lot_number' },
            { data: 'qty', name: 'qty' }
        ],
        order: [[1, 'desc']],
        stateSave: true,
        paging: true,
        searching: false,
        ordering: true,
        info: false,
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50],
        lengthChange: false
    });

    const inputElement = document.getElementById('qrcode');
    const notificationElement = document.getElementById('notification');

    inputElement.addEventListener('paste', function(e) {
        e.preventDefault();
        var pasteData = e.clipboardData.getData('text/plain');
        inputElement.value = pasteData.trim();
        setTimeout(() => {
            processQRData(pasteData.trim());
        }, 2000);
    });

    function processQRData(qrData) {
        const dataArray = qrData.split('|');

        if (dataArray.length >= 4) {
            const formData = new FormData(document.getElementById('dataForm'));
            formData.append('tgl_bln_thn', new Date().toISOString().slice(0, 19).replace('T', ' '));
            formData.append('max_model', dataArray[0]);
            formData.append('qty', dataArray[2]);
            formData.append('lot_number', dataArray[3]);
            formData.append('flag', 1);

            fetch("{{ route('delivery.store') }}", {
                method: "POST",
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayNotification('Data berhasil disimpan!', 'success');
                    playNotificationSound('success');
                    inputElement.value = ""; 
                    table.ajax.reload(); 
                } else {
                    displayNotification('Gagal menyimpan data: ' + data.message, 'danger');
                    playNotificationSound('error'); 
                    inputElement.value = ""; 
                }
            })
            .catch(error => {
                displayNotification('Terjadi kesalahan: ' + error.message, 'danger');
                playNotificationSound('error'); 
            });
        } else {
            displayNotification('Format data QR Code tidak valid.', 'warning');
            playNotificationSound('error'); 
            inputElement.value = ""; 
        }
    }

    function displayNotification(message, type) {
        notificationElement.textContent = message;
        notificationElement.className = `alert alert-${type}`;
        notificationElement.style.display = 'block';

        setTimeout(() => {
            notificationElement.style.display = 'none';
        }, 10000);
    }

    function playNotificationSound(type) {
        const context = new (window.AudioContext || window.webkitAudioContext)();
        let oscillator = context.createOscillator();
        let gainNode = context.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(context.destination);

        if (type === 'success') {
            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(780, context.currentTime);
            oscillator.frequency.exponentialRampToValueAtTime(1760, context.currentTime + 0.3);
        } else if (type === 'error') {
            oscillator.type = 'triangle';
            oscillator.frequency.setValueAtTime(220, context.currentTime);
            oscillator.frequency.exponentialRampToValueAtTime(110, context.currentTime + 0.3);
        } else if (type === 'warning') {
            oscillator.type = 'square';
            oscillator.frequency.setValueAtTime(440, context.currentTime);
            setTimeout(() => oscillator.frequency.setValueAtTime(660, context.currentTime + 0.2), 200);
        }

        gainNode.gain.setValueAtTime(0, context.currentTime);
        gainNode.gain.linearRampToValueAtTime(1, context.currentTime + 0.1);
        gainNode.gain.exponentialRampToValueAtTime(0.001, context.currentTime + 0.5);

        oscillator.start();
        setTimeout(() => oscillator.stop(), 2000); 
    }

    // Fungsi notifikasi suara menggunakan perulangan
    // function playNotificationSound(type, repeat = 2, interval = 1000) {
    //     const context = new (window.AudioContext || window.webkitAudioContext)();

    //     function playSound() {
    //         const oscillator = context.createOscillator();
    //         const gainNode = context.createGain();

    //         oscillator.connect(gainNode);
    //         gainNode.connect(context.destination);

    //         if (type === 'success') {
    //             oscillator.type = 'sine';
    //             oscillator.frequency.setValueAtTime(780, context.currentTime);
    //             oscillator.frequency.exponentialRampToValueAtTime(1760, context.currentTime + 0.3);
    //         } else if (type === 'error') {
    //             oscillator.type = 'triangle';
    //             oscillator.frequency.setValueAtTime(220, context.currentTime);
    //             oscillator.frequency.exponentialRampToValueAtTime(110, context.currentTime + 0.3);
    //         } else if (type === 'warning') {
    //             oscillator.type = 'square';
    //             oscillator.frequency.setValueAtTime(440, context.currentTime);
    //             setTimeout(() => oscillator.frequency.setValueAtTime(660, context.currentTime + 0.2), 200);
    //         }

    //         gainNode.gain.setValueAtTime(0, context.currentTime);
    //         gainNode.gain.linearRampToValueAtTime(1, context.currentTime + 0.1);
    //         gainNode.gain.exponentialRampToValueAtTime(0.001, context.currentTime + 0.5);

    //         oscillator.start();
    //         setTimeout(() => oscillator.stop(), 1000); 
    //     }

    //     let count = 0;

    //     function repeatSound() {
    //         if (count < repeat) {
    //             playSound();
    //             count++;
    //             setTimeout(repeatSound, interval); 
    //         }
    //     }

    //     repeatSound(); 
    // }
});
</script>

<script>
     function resetForm() {
        var form = document.getElementById('dataForm');
        form.reset();
        document.getElementById('qrcode').focus();

        document.getElementById('record-table').style.display = 'none';
        document.getElementById('delivery-table').style.display = 'none';
        
        const notification = document.getElementById('notification');
        notification.style.display = 'none';
        notification.className = 'alert'; 
        notification.textContent = ''; 
    }

    function compareQty() {
        fetch("{{ route('delivery.compare') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            const notification = document.getElementById('notification');
            const recordTable = document.getElementById('record-table');
            const deliveryTable = document.getElementById('delivery-table');
            
            if (data.success) {
                notification.classList.add('alert-success');
                notification.textContent = data.message;
                playNotificationSound('success');
            } else {
                notification.classList.add('alert-danger');
                notification.textContent = data.message;
                playNotificationSound('error');
            }
            notification.style.display = 'block';
            recordTable.style.display = 'block';
            deliveryTable.style.display = 'block';

            let recordDataHTML = '';
            data.recordData.forEach(record => {
                recordDataHTML += `
                    <tr>
                        <td>${record.no_transaksi}</td>
                        <td>${record.model}</td>
                        <td>${record.qty}</td>
                    </tr>
                `;
            });
            document.getElementById('record-data').innerHTML = recordDataHTML;

            let deliveryDataHTML = '';
            data.deliveryData.forEach(delivery => {
                deliveryDataHTML += `
                    <tr>
                        <td>${delivery.no_transaksi}</td>
                        <td>${delivery.max_model}</td>
                        <td>${delivery.qty}</td> 
                    </tr>
                `;
            });
            document.getElementById('delivery-data').innerHTML = deliveryDataHTML;
        })
        .catch(error => {
            console.error('Error:', error);
            const notification = document.getElementById('notification');
            notification.classList.add('alert-danger');
            notification.textContent = 'Gagal membandingkan data.';
            notification.style.display = 'block';
            playNotificationSound('error');
        });
    }

    function playNotificationSound(type) {
        const context = new (window.AudioContext || window.webkitAudioContext)();
        let oscillator = context.createOscillator();
        let gainNode = context.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(context.destination);

        if (type === 'success') {
            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(780, context.currentTime);
            oscillator.frequency.exponentialRampToValueAtTime(1760, context.currentTime + 0.3);
        } else if (type === 'error') {
            oscillator.type = 'triangle';
            oscillator.frequency.setValueAtTime(220, context.currentTime);
            oscillator.frequency.exponentialRampToValueAtTime(110, context.currentTime + 0.3);
        } else if (type === 'warning') {
            oscillator.type = 'square';
            oscillator.frequency.setValueAtTime(440, context.currentTime);
            setTimeout(() => oscillator.frequency.setValueAtTime(660, context.currentTime + 0.2), 200);
        }

        gainNode.gain.setValueAtTime(0, context.currentTime);
        gainNode.gain.linearRampToValueAtTime(1, context.currentTime + 0.1);
        gainNode.gain.exponentialRampToValueAtTime(0.001, context.currentTime + 0.5);

        oscillator.start();
        setTimeout(() => oscillator.stop(), 2000); 
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

<!-- <script>
    let typingTimer;
    const inputElement = document.getElementById('qrcode');
    const notificationElement = document.getElementById('notification');

    inputElement.addEventListener('input', function () {
        clearTimeout(typingTimer); 
        typingTimer = setTimeout(() => {
            const qrData = inputElement.value;
            const dataArray = qrData.split('|');

            if (dataArray.length >= 4) {
                const formData = new FormData(document.getElementById('dataForm'));
                formData.append('tgl_bln_thn', new Date().toISOString().slice(0, 19).replace('T', ' '));
                formData.append('max_model', dataArray[0]);
                formData.append('qty', dataArray[2]);
                formData.append('lot_number', dataArray[3]);
                formData.append('flag', 1);

                fetch("{{ route('delivery.store') }}", {
                    method: "POST",
                    body: formData,
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            displayNotification('Data berhasil disimpan!', 'success');
                            inputElement.value = ""; 
                        } else {
                            displayNotification('Gagal menyimpan data: ' + data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        displayNotification('Terjadi kesalahan: ' + error.message, 'danger');
                    });
            } else {
                displayNotification('Format data QR Code tidak valid.', 'warning');
            }
        }, 3000); 
    });

    function displayNotification(message, type) {
        notificationElement.textContent = message;
        notificationElement.className = `alert alert-${type}`; 
        notificationElement.style.display = 'block'; 

        setTimeout(() => {
            notificationElement.style.display = 'none';
        }, 10000);
    }
</script> -->

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

    .table-header th {
        color: #fff; 
        background-color: #2088ef;
    }
</style>


@endsection
