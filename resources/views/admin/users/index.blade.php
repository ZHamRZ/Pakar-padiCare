@extends('layouts.app')

@section('title', 'Data Pengguna')
@section('page-title', 'Data Pengguna')

@section('content')
<div class="card">
    <div class="card-header">Daftar Pengguna Petani</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Status Email</th>
                        <th>No. HP</th>
                        <th>Riwayat</th>
                        <th>Tanggal Daftar</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $user->nama }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email ?? '-' }}</td>
                        <td>
                            @if($user->email_verified_at)
                            <span class="badge bg-success">Terverifikasi</span>
                            @elseif($user->email)
                            <span class="badge bg-warning text-dark">Belum Verifikasi</span>
                            <form action="{{ route('admin.users.verify', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">Verifikasi Manual</button>
                            </form>
                            @else
                            <span class="badge bg-secondary">Tidak Ada Email</span>
                            @endif
                        </td>
                        <td>{{ $user->no_telepon ?? '-' }}</td>
                        <td>{{ $user->rekomendasi_count }} riwayat</td>
                        <td>{{ optional($user->created_at)->format('d M Y H:i') }}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                onclick="showResetPasswordModal('{{ $user->id }}', '{{ $user->nama }}')">
                                Reset Password
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                onclick="showDeleteModal('{{ $user->id }}', '{{ $user->nama }}')">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Belum ada pengguna petani.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer">{{ $users->links() }}</div>
    @endif
</div>

<!-- Modal Konfirmasi Reset Password -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title fw-bold" id="resetPasswordModalLabel">Reset Password Pengguna</h5>
            </div>
            <div class="modal-body py-3">
                <p class="mb-2">Apakah Anda yakin ingin mereset password pengguna berikut?</p>
                <div class="alert alert-warning mb-0">
                    <strong id="resetUserName"></strong><br>
                    <small>Password akan direset ke: <code>petani123</code></small>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                <form id="resetPasswordForm" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Pengguna -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title fw-bold text-danger" id="deleteUserModalLabel">Hapus Akun Pengguna</h5>
            </div>
            <div class="modal-body py-3">
                <p class="mb-2">Apakah Anda yakin ingin menghapus akun pengguna berikut?</p>
                <div class="alert alert-danger mb-0">
                    <strong id="deleteUserName"></strong><br>
                    <small class="text-danger">Tindakan ini tidak dapat dibatalkan!</small>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                <form id="deleteUserForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Hapus Akun
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi menampilkan modal reset password
function showResetPasswordModal(userId, userName) {
    document.getElementById('resetUserName').textContent = userName;
    document.getElementById('resetPasswordForm').action = '{{ url("admin/users") }}/' + userId + '/reset-password';
    
    const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
    modal.show();
}

// Fungsi menampilkan modal hapus user
function showDeleteModal(userId, userName) {
    document.getElementById('deleteUserName').textContent = userName;
    document.getElementById('deleteUserForm').action = '{{ url("admin/users") }}/' + userId;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
    modal.show();
}

// Handle form submission dengan AJAX untuk toast notification
document.addEventListener('DOMContentLoaded', function() {
    // Reset Password Form
    document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Disable button dan tampilkan loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Memproses...';
        
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Tutup modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal'));
            modal.hide();
            
            // Tampilkan toast notification
            if (data.success) {
                showSuccessToast(data.message || 'Password berhasil direset!');
            } else {
                showErrorToast(data.message || 'Gagal mereset password.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('Terjadi kesalahan saat memproses permintaan.');
        })
        .finally(() => {
            // Kembalikan tombol ke keadaan semula
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
    
    // Delete User Form
    document.getElementById('deleteUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Disable button dan tampilkan loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menghapus...';
        
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Tutup modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteUserModal'));
            modal.hide();
            
            // Tampilkan toast notification
            if (data.success) {
                showSuccessToast(data.message || 'Pengguna berhasil dihapus!');
                // Refresh halaman setelah jeda singkat
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showErrorToast(data.message || 'Gagal menghapus pengguna.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('Terjadi kesalahan saat memproses permintaan.');
        })
        .finally(() => {
            // Kembalikan tombol ke keadaan semula
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
});
</script>
@endsection