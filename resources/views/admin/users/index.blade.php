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
                                data-bs-toggle="modal" 
                                data-bs-target="#resetPasswordModal"
                                data-user-id="{{ $user->id }}"
                                data-user-name="{{ $user->nama }}">
                                Reset Password
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteUserModal"
                                data-user-id="{{ $user->id }}"
                                data-user-name="{{ $user->nama }}">
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
                    <strong id="userNameDisplay"></strong>
                    <hr class="my-2">
                    <small class="text-muted">Password akan direset ke: <strong>petani123</strong></small>
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
                    <strong id="userNameDisplayDelete"></strong>
                    <hr class="my-2">
                    <small class="text-muted">Tindakan ini tidak dapat dibatalkan. Semua data riwayat pengguna akan ikut terhapus.</small>
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle Reset Password Modal
    const resetPasswordModal = document.getElementById('resetPasswordModal');
    if (resetPasswordModal) {
        resetPasswordModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');
            
            document.getElementById('userNameDisplay').textContent = userName;
            document.getElementById('resetPasswordForm').action = '{{ route("admin.users.resetPassword", "__ID__") }}'.replace('__ID__', userId);
        });
    }
    
    // Handle Delete User Modal
    const deleteUserModal = document.getElementById('deleteUserModal');
    if (deleteUserModal) {
        deleteUserModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');
            
            document.getElementById('userNameDisplayDelete').textContent = userName;
            document.getElementById('deleteUserForm').action = '{{ route("admin.users.destroy", "__ID__") }}'.replace('__ID__', userId);
        });
    }
});
</script>
@endpush