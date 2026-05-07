<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\ProjectImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

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

            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'username')->ignore($user->id),
            ],

            'email' => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'alamat' => 'nullable|string',
            'catatan_profil' => 'nullable|string',

            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'password_lama' => 'nullable|string',

            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        /**
         * Update password
         */
        if (!empty($validated['password'])) {

            if (
                empty($validated['password_lama']) ||
                !Hash::check($validated['password_lama'], $user->password)
            ) {
                return back()
                    ->withInput(
                        $request->except([
                            'password',
                            'password_confirmation',
                            'password_lama',
                        ])
                    )
                    ->withErrors([
                        'password_lama' => 'Password lama tidak sesuai.',
                    ]);
            }

            $user->password = Hash::make($validated['password']);
        }

        /**
         * Upload foto profil
         */
        if ($request->hasFile('foto_profil')) {

            ProjectImage::delete($user->foto_profil);

            $user->foto_profil = ProjectImage::store(
                $request->file('foto_profil'),
                'profil'
            );
        }

        /**
         * Update data profil
         */
        $user->fill([
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'alamat' => $validated['alamat'] ?? null,
            'catatan_profil' => $validated['catatan_profil'] ?? null,
        ]);

        /**
         * Update email + reset verifikasi jika email berubah
         */
        if (($validated['email'] ?? null) !== $user->email) {

            $user->email = $validated['email'] ?? null;

            // Jika email diubah / dikosongkan maka reset verifikasi
            $user->email_verified_at = null;

            // Generate token hanya jika email ada
            $user->email_verification_token =
                !empty($validated['email'])
                ? Str::random(60)
                : null;

            /**
             * Kirim email verifikasi
             * Aktifkan jika mail sudah dikonfigurasi
             */
            /*
            if (!empty($validated['email'])) {
                $this->sendVerificationEmail($user);
            }
            */
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Verifikasi email berdasarkan token
     */
    public function verifyEmail($token)
    {
        $user = User::where(
            'email_verification_token',
            $token
        )->firstOrFail();

        $user->update([
            'email_verified_at' => now(),
            'email_verification_token' => null,
        ]);

        return redirect()
            ->route('user.profile.edit')
            ->with('success', 'Email berhasil diverifikasi!');
    }

    /**
     * Kirim ulang email verifikasi
     */
    public function sendVerificationEmail(Request $request)
    {
        $user = Auth::user();

        if (empty($user->email)) {
            return back()->withErrors([
                'profile' => 'Email belum diisi.',
            ]);
        }

        // Generate token baru jika belum ada
        if (empty($user->email_verification_token)) {
            $user->email_verification_token = Str::random(60);
            $user->save();
        }

        $this->sendEmailVerification($user);

        return back()->with('success', 'Link verifikasi berhasil dikirim.');
    }

    /**
     * Fungsi private kirim email verifikasi
     */
    private function sendEmailVerification($user)
    {
        $url = route('profile.verify.email', [
            'token' => $user->email_verification_token,
        ]);

        /**
         * Aktifkan jika mail sudah dikonfigurasi
         */
        // Mail::to($user->email)->send(new VerifyEmailMail($url));

        // Temporary: tampilkan link di session
        session()->flash('verification_link', $url);
    }
}
