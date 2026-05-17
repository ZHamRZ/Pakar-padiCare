<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Rekomendasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user
     */
    public function index()
    {
        $users = User::where('role', 'petani')
            ->withCount('rekomendasi')
            ->latest()
            ->paginate(10);

        return view('admin.pengguna.index', compact('users'));
    }

    /**
     * Verifikasi email manual oleh admin
     */
    public function verifyEmailManual($id)
    {
        $user = User::findOrFail($id);

        // Tidak perlu verifikasi jika email kosong
        if (empty($user->email)) {
            return back()->with(
                'error',
                'User belum memiliki email.'
            );
        }

        $user->update([
            'email_verified_at' => now(),
            'email_verification_token' => null,
        ]);

        return back()->with(
            'success',
            'Email user berhasil diverifikasi manual oleh Admin.'
        );
    }

    /**
     * Hapus user
     */
    public function destroy(User $user, Request $request)
    {
        // Cegah admin dihapus
        if ($user->role === 'admin') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun admin tidak dapat dihapus.'
                ], 403);
            }
            return back()->with(
                'error',
                'Akun admin tidak dapat dihapus.'
            );
        }

        $user->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Akun pengguna berhasil dihapus.'
            ]);
        }

        return redirect()
            ->route('admin.pengguna.index')
            ->with(
                'success',
                'Akun pengguna berhasil dihapus.'
            );
    }

    /**
     * Reset password user
     */
    public function resetPassword(User $user, Request $request)
    {
        $user->update([
            'password' => Hash::make('petani123')
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset ke: petani123'
            ]);
        }

        return back()->with(
            'success',
            'Password berhasil direset ke: petani123'
        );
    }
}
