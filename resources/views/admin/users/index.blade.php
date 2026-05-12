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
                                onclick="showDeleteUserModal('{{ $user->id }}', '{{ $user->nama }}')">
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
                <p class="mb-2">Apakah Anda yakin ingin mereset password pengguna ini?</p>
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Password baru:</strong> <code>petani123</code>
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
                <h5 class="modal-title fw-bold" id="deleteUserModalLabel">Hapus Akun Pengguna</h5>
            </div>
            <div class="modal-body py-3">
                <p class="mb-0">Apakah Anda yakin ingin menghapus akun pengguna ini? Tindakan ini tidak dapat dibatalkan.</p>
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
@endsection

@push('scripts')
<script>
// Variabel global untuk menyimpan modal instances
let resetPasswordModalInstance = null;
let deleteUserModalInstance = null;

document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi modal instances
    const resetPasswordModalEl = document.getElementById('resetPasswordModal');
    const deleteUserModalEl = document.getElementById('deleteUserModal');
    
    if (resetPasswordModalEl) {
        resetPasswordModalInstance = new bootstrap.Modal(resetPasswordModalEl);
    }
    
    if (deleteUserModalEl) {
        deleteUserModalInstance = new bootstrap.Modal(deleteUserModalEl);
    }
});

// Fungsi untuk menampilkan modal reset password
function showResetPasswordModal(userId, userName) {
    const form = document.getElementById('resetPasswordForm');
    const modalTitle = document.getElementById('resetPasswordModalLabel');
    
    if (form && resetPasswordModalInstance) {
        form.action = `/admin/users/${userId}/reset-password`;
        modalTitle.textContent = `Reset Password: ${userName}`;
        resetPasswordModalInstance.show();
    }
}

// Fungsi untuk menampilkan modal hapus pengguna
function showDeleteUserModal(userId, userName) {
    const form = document.getElementById('deleteUserForm');
    const modalTitle = document.getElementById('deleteUserModalLabel');
    
    if (form && deleteUserModalInstance) {
        form.action = `/admin/users/${userId}`;
        modalTitle.textContent = `Hapus Akun: ${userName}`;
        deleteUserModalInstance.show();
    }
}

// Handle form submission untuk reset password
document.addEventListener('DOMContentLoaded', function() {
    const resetPasswordForm = document.getElementById('resetPasswordForm');
    const deleteUserForm = document.getElementById('deleteUserForm');
    
    if (resetPasswordForm) {
        resetPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const actionUrl = this.action;
            
            fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(Object.fromEntries(formData))
            })
            .then(response => response.json())
            .then(data => {
                if (resetPasswordModalInstance) {
                    resetPasswordModalInstance.hide();
                }
                
                if (data.success) {
                    showSuccessToast(data.message || 'Password berhasil direset.');
                    // Reload halaman untuk update data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showErrorToast(data.message || 'Gagal mereset password.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (resetPasswordModalInstance) {
                    resetPasswordModalInstance.hide();
                }
                showErrorToast('Terjadi kesalahan saat mereset password.');
            });
        });
    }
    
    if (deleteUserForm) {
        deleteUserForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const actionUrl = this.action;
            
            fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    _method: 'DELETE'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (deleteUserModalInstance) {
                    deleteUserModalInstance.hide();
                }
                
                if (data.success) {
                    showSuccessToast(data.message || 'Pengguna berhasil dihapus.');
                    // Reload halaman untuk update data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showErrorToast(data.message || 'Gagal menghapus pengguna.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (deleteUserModalInstance) {
                    deleteUserModalInstance.hide();
                }
                showErrorToast('Terjadi kesalahan saat menghapus pengguna.');
            });
        });
    }
});
</script>
@endpush