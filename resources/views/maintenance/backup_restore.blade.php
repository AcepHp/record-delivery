@extends('layouts.appadmn')

@section('header')
<h2 class="text-3xl font-semibold text-gray-800 dark:text-gray-200">
    Backup & Restore Data Delivery
</h2>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row">
            <!-- Backup Card -->
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h4>Backup Data</h4>
                        <form id="backupForm" action="{{ route('maintenance.backup') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="start_date_backup" class="form-label">Start Date</label>
                                <input type="date" id="start_date_backup" name="start_date" class="form-control"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="end_date_backup" class="form-label">End Date</label>
                                <input type="date" id="end_date_backup" name="end_date" class="form-control" required>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="openPasswordModal('backup')">Backup
                                Data</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Restore Card -->
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h4>Restore Data</h4>
                        <form id="restoreForm" action="{{ route('maintenance.restore') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="start_date_restore" class="form-label">Start Date</label>
                                <input type="date" id="start_date_restore" name="start_date" class="form-control"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="end_date_restore" class="form-label">End Date</label>
                                <input type="date" id="end_date_restore" name="end_date" class="form-control" required>
                            </div>
                            <button type="button" class="btn btn-success" onclick="openPasswordModal('restore')">Restore
                                Data</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Password Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="passwordForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordModalLabel">Masukkan Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="password_input" class="form-label">Password</label>
                        <input type="password" id="password_input" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Lanjutkan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openPasswordModal(actionType) {
    const form = document.getElementById('passwordForm');

    if (actionType === 'backup') {
        form.action = "{{ route('maintenance.backup') }}";
        // Ambil tanggal dari form utama
        form.innerHTML +=
            `<input type="hidden" name="start_date" value="${document.getElementById('start_date_backup').value}">
                        <input type="hidden" name="end_date" value="${document.getElementById('end_date_backup').value}">`;
    } else if (actionType === 'restore') {
        form.action = "{{ route('maintenance.restore') }}";
        form.innerHTML +=
            `<input type="hidden" name="start_date" value="${document.getElementById('start_date_restore').value}">
                        <input type="hidden" name="end_date" value="${document.getElementById('end_date_restore').value}">`;
    }

    const modal = new bootstrap.Modal(document.getElementById('passwordModal'));
    modal.show();
}
</script>
@endsection