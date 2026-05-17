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
                            <img src="{{ $user->foto_profil_url }}" alt="{{ $user->nama }}" class="rounded-circle"
                                width="40" height="40" style="object-fit: cover;">
                            @else
                            <span
                                class="avatar-fallback d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary text-white fw-bold"
                                style="width: 40px; height: 40px; font-size: 0.9rem;">
                                {{ strtoupper(substr($user->nama, 0, 1)) }}
                            </span>
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
                            <form action="{{ route('admin.pengguna.verify', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">Verifikasi Manual</button>
                            </form>
                            @else
                            <span class="badge bg-secondary">Tidak Ada Email</span>
                            @endif
                        </td>
                        <td>{{ $user->rekomendasi_count }} riwayat</td>
                        <td>{{ optional($user->created_at)->format('d M Y H:i') }}</td>
                        <td class="text-end">
                            {{-- Gunakan data-attribute, bukan inline onclick --}}
                            <button type="button" class="btn btn-sm btn-outline-warning btn-reset-password"
                                data-user-id="{{ $user->id }}" data-user-name="{{ $user->nama }}">
                                Reset Password
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-user"
                                data-user-id="{{ $user->id }}" data-user-name="{{ $user->nama }}">
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

<!-- Modal Reset Password -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel"
    aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title fw-bold" id="resetPasswordModalLabel">
                    <i class="bi bi-key-fill text-warning me-2"></i>Reset Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <p class="mb-2">Apakah Anda yakin ingin mereset password untuk pengguna:</p>
                <div class="alert alert-warning d-flex align-items-center mb-2">
                    <i class="bi bi-person-circle fs-4 me-2"></i>
                    <strong id="resetPasswordUserName">-</strong>
                </div>
                <small class="text-muted">Password akan direset ke: <code>petani123</code></small>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    id="resetPasswordCancelBtn">Batal</button>
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
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header border-0 pb-2 bg-danger bg-opacity-10">
                <h5 class="modal-title fw-bold text-danger" id="deleteUserModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Hapus Pengguna
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <p class="mb-2 fw-semibold text-danger">Peringatan! Tindakan ini tidak dapat dibatalkan.</p>
                <p class="mb-2">Apakah Anda yakin ingin menghapus akun pengguna:</p>
                <div class="alert alert-danger d-flex align-items-center mb-2">
                    <i class="bi bi-person-x-fill fs-4 me-2"></i>
                    <strong id="deleteUserName">-</strong>
                </div>
                <small class="text-muted">Semua data terkait pengguna ini akan dihapus secara permanen.</small>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    id="deleteUserCancelBtn">Batal</button>
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
document.addEventListener('DOMContentLoaded', function() {

    // ─── State ───────────────────────────────────────────────────────────────
    let isProcessing = false;
    let currentUserId = null;
    let currentRowEl = null;

    // ─── Inisialisasi modal (sekali saja) ────────────────────────────────────
    const resetModal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));

    // ─── Helper: set loading state ───────────────────────────────────────────
    function setLoading(type, loading) {
        isProcessing = loading;

        const prefix = type === 'reset' ? 'resetPassword' : 'deleteUser';
        const confirmBtn = document.getElementById(`${prefix}ConfirmBtn`);
        const cancelBtn = document.getElementById(`${prefix}CancelBtn`);
        const spinner = document.getElementById(`${prefix}Spinner`);
        const icon = document.getElementById(`${prefix}Icon`);

        confirmBtn.disabled = loading;
        cancelBtn.disabled = loading;
        spinner.classList.toggle('d-none', !loading);
        icon.classList.toggle('d-none', loading);
    }

    // ─── Helper: reset state saat modal ditutup ──────────────────────────────
    function onModalHidden(type) {
        if (isProcessing) return; // jangan reset jika masih proses
        isProcessing = false;
        currentUserId = null;
        currentRowEl = null;
        setLoading(type, false);
    }

    document.getElementById('resetPasswordModal').addEventListener('hidden.bs.modal', () => onModalHidden(
        'reset'));
    document.getElementById('deleteUserModal').addEventListener('hidden.bs.modal', () => onModalHidden(
        'delete'));

    // ─── Buka modal Reset Password via event delegation ──────────────────────
    document.addEventListener('click', function(e) {
        const resetBtn = e.target.closest('.btn-reset-password');
        if (resetBtn) {
            if (isProcessing) return;
            currentUserId = resetBtn.dataset.userId;
            currentRowEl = resetBtn.closest('tr');
            document.getElementById('resetPasswordUserName').textContent = resetBtn.dataset.userName;
            setLoading('reset', false); // pastikan state bersih
            resetModal.show();
            return;
        }

        const deleteBtn = e.target.closest('.btn-delete-user');
        if (deleteBtn) {
            if (isProcessing) return;
            currentUserId = deleteBtn.dataset.userId;
            currentRowEl = deleteBtn.closest('tr');
            document.getElementById('deleteUserName').textContent = deleteBtn.dataset.userName;
            setLoading('delete', false); // pastikan state bersih
            deleteModal.show();
        }
    });

    // ─── Konfirmasi Reset Password ───────────────────────────────────────────
    document.getElementById('resetPasswordConfirmBtn').addEventListener('click', async function() {
        if (isProcessing || !currentUserId) return;
        setLoading('reset', true);

        try {
            const response = await fetch(`/admin/pengguna/${currentUserId}/reset-password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                        .content,
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (response.ok && data.success) {
                resetModal.hide();
                window.showSuccessToast?.(data.message || 'Password berhasil direset.');
            } else {
                window.showErrorToast?.(data.message || 'Gagal mereset password.');
                setLoading('reset', false);
            }
        } catch (err) {
            console.error(err);
            window.showErrorToast?.('Terjadi kesalahan pada sistem. Silakan coba lagi.');
            setLoading('reset', false);
        }
    });

    // ─── Konfirmasi Hapus Pengguna ────────────────────────────────────────────
    document.getElementById('deleteUserConfirmBtn').addEventListener('click', async function() {
        if (isProcessing || !currentUserId) return;
        setLoading('delete', true);

        try {
            const response = await fetch(`/admin/pengguna/${currentUserId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                        .content,
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (response.ok && data.success) {
                deleteModal.hide();
                window.showSuccessToast?.(data.message || 'Pengguna berhasil dihapus.');

                // Hapus baris dari tabel dengan animasi
                if (currentRowEl) {
                    currentRowEl.style.transition = 'opacity 0.3s ease';
                    currentRowEl.style.opacity = '0';
                    setTimeout(() => {
                        currentRowEl.remove();
                        const tbody = document.querySelector('tbody');
                        if (!tbody.querySelector('tr')) {
                            tbody.innerHTML =
                                '<tr><td colspan="8" class="text-center py-4 text-muted">Belum ada pengguna petani.</td></tr>';
                        }
                    }, 300);
                }
            } else {
                window.showErrorToast?.(data.message || 'Gagal menghapus pengguna.');
                setLoading('delete', false);
            }
        } catch (err) {
            console.error(err);
            window.showErrorToast?.('Terjadi kesalahan pada sistem. Silakan coba lagi.');
            setLoading('delete', false);
        }
    });

});
</script>
@endpush
