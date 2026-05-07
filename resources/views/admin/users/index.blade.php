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
                            <form action="{{ route('admin.users.resetPassword', $user) }}" method="POST"
                                class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning">Reset Password</button>
                            </form>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Hapus akun pengguna ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
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
@endsection