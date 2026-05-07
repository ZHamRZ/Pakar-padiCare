<?php

namespace App\Http\Controllers;

use App\Support\ProjectImage;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Auth\Access\AuthorizationException;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'no_telp' => ['nullable', 'string', 'max:30', Rule::unique('users', 'no_telp')->ignore($user->id)],
            'alamat' => 'nullable|string',
            'catatan_profil' => 'nullable|string',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'password_lama' => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if (!empty($validated['password'])) {
            if (empty($validated['password_lama']) || !Hash::check($validated['password_lama'], $user->password)) {
                return back()
                    ->withInput($request->except(['password', 'password_confirmation', 'password_lama']))
                    ->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
            }

            $user->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('foto_profil')) {
            ProjectImage::delete($user->foto_profil);
            $user->foto_profil = ProjectImage::store($request->file('foto_profil'), 'profil');
        }

        $emailChanged = $user->email !== $validated['email'];

        $user->fill([
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
            'no_telp' => $validated['no_telp'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'catatan_profil' => $validated['catatan_profil'] ?? null,
        ]);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function sendVerificationEmail(Request $request)
    {
        $user = $request->user();

        if (!$user->email) {
            return back()->withErrors(['email' => 'Isi email terlebih dahulu sebelum melakukan verifikasi.']);
        }

        if ($user->hasVerifiedEmail()) {
            return back()->with('success', 'Email Anda sudah terverifikasi.');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('success', 'Email verifikasi berhasil dikirim. Silakan cek inbox email Anda.');
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            $route = $request->user()->isAdmin() ? 'admin.profile.edit' : 'user.profile.edit';
            return redirect()->route($route)->with('success', 'Email sudah diverifikasi sebelumnya.');
        }

        try {
            $request->fulfill();
            event(new Verified($request->user()));
        } catch (AuthorizationException $e) {
            return redirect()->route('verification.notice')->with('error', 'Link verifikasi tidak valid atau sudah kadaluarsa.');
        }

        $route = $request->user()->isAdmin() ? 'admin.profile.edit' : 'user.profile.edit';

        return redirect()->route($route)->with('success', 'Email berhasil diverifikasi.');
    }

    public function verifyEmailByToken($id, $hash)
    {
        $user = \App\Models\User::find($id);

        if (!$user) {
            abort(404, 'User tidak ditemukan.');
        }

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Link verifikasi tidak valid.');
        }

        if ($user->hasVerifiedEmail()) {
            $route = $user->isAdmin() ? 'admin.profile.edit' : 'user.profile.edit';
            return redirect()->route($route)->with('success', 'Email sudah diverifikasi sebelumnya.');
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        $route = $user->isAdmin() ? 'admin.profile.edit' : 'user.profile.edit';

        return redirect()->route($route)->with('success', 'Email berhasil diverifikasi.');
    }
}
