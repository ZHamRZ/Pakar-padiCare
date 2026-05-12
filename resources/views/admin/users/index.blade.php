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
                        <th>Foto</th>
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
                        <td>
                            @if($user->foto_profil_url)
                                <img src="{{ $user->foto_profil_url }}" alt="{{ $user->nama }}" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                            @else
                                <span class="avatar-fallback d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary text-white fw-bold" style="width: 40px; height: 40px; font-size: 0.9rem;">{{ strtoupper(substr($user->nama, 0, 1)) }}</span>
                            @endif
                        </td>
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
                                    onclick="openResetPasswordModal({{ $user->id }}, '{{ addslashes($user->nama) }}')">
                                Reset Password
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                    onclick="openDeleteUserModal({{ $user->id }}, '{{ addslashes($user->nama) }}')">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Belum ada pengguna petani.</td>
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

<!-- Modal Reset Password -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title fw-bold" id="resetPasswordModalLabel">
                    <i class="bi bi-key-fill text-warning me-2"></i>Reset Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" {{ (session('error') || session('success')) ? '' : '' }}></button>
            </div>
            <div class="modal-body py-3">
                <p class="mb-2">Apakah Anda yakin ingin mereset password untuk pengguna:</p>
                <div class="alert alert-warning d-flex align-items-center mb-0">
                    <i class="bi bi-person-circle fs-4 me-2"></i>
                    <strong id="resetPasswordUserName">-</strong>
                </div>
                <small class="text-muted">Password akan direset ke: <code>petani123</code></small>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="resetPasswordCancelBtn">Batal</button>
                <button type="button" class="btn btn-warning" id="resetPasswordConfirmBtn">
                    <span class="spinner-border spinner-border-sm me-1 d-none" id="resetPasswordSpinner"></span>
                    <i class="bi bi-arrow-counterclockwise me-1" id="resetPasswordIcon"></i>
                    Reset Password
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus Pengguna -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header border-0 pb-2 bg-danger bg-opacity-10">
                <h5 class="modal-title fw-bold text-danger" id="deleteUserModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Hapus Pengguna
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" {{ (session('error') || session('success')) ? '' : '' }}></button>
            </div>
            <div class="modal-body py-3">
                <p class="mb-2 fw-semibold text-danger">Peringatan! Tindakan ini tidak dapat dibatalkan.</p>
                <p class="mb-2">Apakah Anda yakin ingin menghapus akun pengguna:</p>
                <div class="alert alert-danger d-flex align-items-center mb-0">
                    <i class="bi bi-person-x-fill fs-4 me-2"></i>
                    <strong id="deleteUserName">-</strong>
                </div>
                <small class="text-muted">Semua data terkait pengguna ini akan dihapus secara permanen.</small>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="deleteUserCancelBtn">Batal</button>
                <button type="button" class="btn btn-danger" id="deleteUserConfirmBtn">
                    <span class="spinner-border spinner-border-sm me-1 d-none" id="deleteUserSpinner"></span>
                    <i class="bi bi-trash-fill me-1" id="deleteUserIcon"></i>
                    Hapus Pengguna
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// State management untuk mencegah duplicate request
let isProcessing = false;
let currentUserId = null;
let resetPasswordModalInstance = null;
let deleteUserModalInstance = null;

// Inisialisasi modal saat DOM siap
document.addEventListener('DOMContentLoaded', function() {
    // Gunakan fungsi Toast global dari layouts/app.blade.php
    
    // Fungsi untuk membuka modal reset password
    window.openResetPasswordModal = function(userId, userName) {
        if (isProcessing) return;
        
        currentUserId = userId;
        document.getElementById('resetPasswordUserName').textContent = userName;
        
        if (!resetPasswordModalInstance) {
            resetPasswordModalInstance = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
            
            // Event listener saat modal ditutup
            document.getElementById('resetPasswordModal').addEventListener('hidden.bs.modal', function() {
                resetState();
            });
        }
        
        resetPasswordModalInstance.show();
    };
    
    // Fungsi untuk membuka modal hapus pengguna
    window.openDeleteUserModal = function(userId, userName) {
        if (isProcessing) return;
        
        currentUserId = userId;
        document.getElementById('deleteUserName').textContent = userName;
        
        if (!deleteUserModalInstance) {
            deleteUserModalInstance = new bootstrap.Modal(document.getElementById('deleteUserModal'));
            
            // Event listener saat modal ditutup
            document.getElementById('deleteUserModal').addEventListener('hidden.bs.modal', function() {
                resetState();
            });
        }
        
        deleteUserModalInstance.show();
    };
    
    // Fungsi untuk reset state setelah aksi selesai
    function resetState() {
        isProcessing = false;
        currentUserId = null;
        
        // Reset tombol reset password
        const resetBtn = document.getElementById('resetPasswordConfirmBtn');
        const resetSpinner = document.getElementById('resetPasswordSpinner');
        const resetIcon = document.getElementById('resetPasswordIcon');
        const resetCancelBtn = document.getElementById('resetPasswordCancelBtn');
        
        resetBtn.disabled = false;
        resetSpinner.classList.add('d-none');
        resetIcon.classList.remove('d-none');
        resetCancelBtn.disabled = false;
        
        // Reset tombol delete user
        const deleteBtn = document.getElementById('deleteUserConfirmBtn');
        const deleteSpinner = document.getElementById('deleteUserSpinner');
        const deleteIcon = document.getElementById('deleteUserIcon');
        const deleteCancelBtn = document.getElementById('deleteUserCancelBtn');
        
        deleteBtn.disabled = false;
        deleteSpinner.classList.add('d-none');
        deleteIcon.classList.remove('d-none');
        deleteCancelBtn.disabled = false;
    }
    
    // Fungsi untuk set loading state
    function setLoading(actionType, isLoading) {
        isProcessing = isLoading;
        
        if (actionType === 'reset') {
            const resetBtn = document.getElementById('resetPasswordConfirmBtn');
            const resetSpinner = document.getElementById('resetPasswordSpinner');
            const resetIcon = document.getElementById('resetPasswordIcon');
            const resetCancelBtn = document.getElementById('resetPasswordCancelBtn');
            
            resetBtn.disabled = isLoading;
            resetCancelBtn.disabled = isLoading;
            
            if (isLoading) {
                resetSpinner.classList.remove('d-none');
                resetIcon.classList.add('d-none');
            } else {
                resetSpinner.classList.add('d-none');
                resetIcon.classList.remove('d-none');
            }
        } else if (actionType === 'delete') {
            const deleteBtn = document.getElementById('deleteUserConfirmBtn');
            const deleteSpinner = document.getElementById('deleteUserSpinner');
            const deleteIcon = document.getElementById('deleteUserIcon');
            const deleteCancelBtn = document.getElementById('deleteUserCancelBtn');
            
            deleteBtn.disabled = isLoading;
            deleteCancelBtn.disabled = isLoading;
            
            if (isLoading) {
                deleteSpinner.classList.remove('d-none');
                deleteIcon.classList.add('d-none');
            } else {
                deleteSpinner.classList.add('d-none');
                deleteIcon.classList.remove('d-none');
            }
        }
    }
    
    // Handler untuk reset password
    document.getElementById('resetPasswordConfirmBtn').addEventListener('click', async function() {
        if (isProcessing || !currentUserId) return;
        
        setLoading('reset', true);
        
        try {
            const response = await fetch(`/admin/users/${currentUserId}/reset-password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                showSuccessToast(data.message || 'Password berhasil direset.');
                resetPasswordModalInstance.hide();
                
                // Refresh halaman untuk update data jika diperlukan
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showErrorToast(data.message || 'Gagal mereset password.');
                setLoading('reset', false);
            }
        } catch (error) {
            console.error('Error:', error);
            showErrorToast('Terjadi kesalahan pada sistem. Silakan coba lagi.');
            setLoading('reset', false);
        }
    });
    
    // Handler untuk hapus pengguna
    document.getElementById('deleteUserConfirmBtn').addEventListener('click', async function() {
        if (isProcessing || !currentUserId) return;
        
        setLoading('delete', true);
        
        try {
            const response = await fetch(`/admin/users/${currentUserId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                showSuccessToast(data.message || 'Pengguna berhasil dihapus.');
                deleteUserModalInstance.hide();
                
                // Hapus baris tabel secara visual
                const row = document.querySelector(`button[onclick*="openDeleteUserModal(${currentUserId})"]`)?.closest('tr');
                if (row) {
                    row.style.transition = 'opacity 0.3s ease';
                    row.style.opacity = '0';
                    setTimeout(() => {
                        row.remove();
                        // Cek apakah tabel kosong
                        const tbody = document.querySelector('tbody');
                        if (tbody.querySelectorAll('tr').length === 0) {
                            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">Belum ada pengguna petani.</td></tr>';
                        }
                    }, 300);
                }
            } else {
                showErrorToast(data.message || 'Gagal menghapus pengguna.');
                setLoading('delete', false);
            }
        } catch (error) {
            console.error('Error:', error);
            showErrorToast('Terjadi kesalahan pada sistem. Silakan coba lagi.');
            setLoading('delete', false);
        }
    });
});
</script>
@endpush