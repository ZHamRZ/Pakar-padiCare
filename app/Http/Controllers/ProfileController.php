<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\ProjectImage;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
        /** @var User $user */
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

            'alamat'         => 'nullable|string',
            'catatan_profil' => 'nullable|string',
            'foto_profil'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'password_lama'  => 'nullable|string',
            'password'       => 'nullable|string|min:6|confirmed',
        ], [
            'nama.required'      => 'Nama wajib diisi.',
            'username.required'  => 'Username wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah digunakan.',
            'password.min'       => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // ── Ganti Password ─────────────────────────────────────────
        if (!empty($validated['password'])) {

            if (
                empty($validated['password_lama']) ||
                !Hash::check($validated['password_lama'], $user->password)
            ) {
                return back()
                    ->withInput($request->except([
                        'password',
                        'password_confirmation',
                        'password_lama',
                    ]))
                    ->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
            }

            $user->password = Hash::make($validated['password']);
        }

        // ── Upload Foto Profil ──────────────────────────────────────
        if ($request->hasFile('foto_profil')) {
            ProjectImage::delete($user->foto_profil);
            $user->foto_profil = ProjectImage::store(
                $request->file('foto_profil'),
                'profil'
            );
        }

        // ── Update Data Profil ─────────────────────────────────────
        $user->fill([
            'nama'           => $validated['nama'],
            'username'       => $validated['username'],
            'alamat'         => $validated['alamat'] ?? null,
            'catatan_profil' => $validated['catatan_profil'] ?? null,
        ]);

        // ── Update Email + Reset Verifikasi jika Email Berubah ─────
        $emailBaru = $validated['email'] ?? null;

        if ($emailBaru !== $user->email) {

            $user->email             = $emailBaru;
            $user->email_verified_at = null;
            $user->email_verification_token = !empty($emailBaru)
                ? Str::random(60)
                : null;

            /**
             * Kirim email verifikasi.
             * Aktifkan jika mail sudah dikonfigurasi di .env
             */
            /*
            if (!empty($emailBaru)) {
                $this->dispatchVerificationEmail($user);
            }
            */
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    // ──────────────────────────────────────────────────────────────
    // VERIFIKASI EMAIL
    // ──────────────────────────────────────────────────────────────

    /**
     * Verifikasi via signed URL Laravel (EmailVerificationRequest).
     * Route: GET /email/verify/{id}/{hash}  [middleware: signed]
     * Nama route: verification.verify
     */
    public function verifyEmail(EmailVerificationRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $route = $user->isAdmin()
            ? 'admin.profile.edit'
            : 'user.profile.edit';

        if ($user->hasVerifiedEmail()) {
            return redirect()->route($route)
                ->with('success', 'Email sudah diverifikasi sebelumnya.');
        }

        try {
            $request->fulfill();
            event(new Verified($user));
        } catch (AuthorizationException $e) {
            return redirect()->route('verification.notice')
                ->with('error', 'Link verifikasi tidak valid atau sudah kadaluarsa.');
        }

        return redirect()->route($route)
            ->with('success', 'Email berhasil diverifikasi.');
    }

    /**
     * Verifikasi via {id} + {hash} sha1 email.
     * Route: GET /profile/verify/{id}/{hash}
     * Nama route: profile.verify.email
     */
    public function verifyEmailByToken(string $id, string $hash): RedirectResponse
    {
        $user = User::find($id);

        if (!$user) {
            abort(404, 'User tidak ditemukan.');
        }

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Link verifikasi tidak valid.');
        }

        $route = $user->isAdmin()
            ? 'admin.profile.edit'
            : 'user.profile.edit';

        if ($user->hasVerifiedEmail()) {
            return redirect()->route($route)
                ->with('success', 'Email sudah diverifikasi sebelumnya.');
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return redirect()->route($route)
            ->with('success', 'Email berhasil diverifikasi.');
    }

    /**
     * Verifikasi via token acak (Str::random) di kolom DB.
     * Route: GET /profile/verify/{token}
     * Nama route: profile.verify.email (token kolom)
     */
    public function verifyEmailByRandomToken(string $token): RedirectResponse
    {
        $user = User::where('email_verification_token', $token)->firstOrFail();

        $route = $user->isAdmin()
            ? 'admin.profile.edit'
            : 'user.profile.edit';

        if ($user->hasVerifiedEmail()) {
            return redirect()->route($route)
                ->with('success', 'Email sudah diverifikasi sebelumnya.');
        }

        $user->update([
            'email_verified_at'        => now(),
            'email_verification_token' => null,
        ]);

        event(new Verified($user));

        return redirect()->route($route)
            ->with('success', 'Email berhasil diverifikasi!');
    }

    // ──────────────────────────────────────────────────────────────
    // KIRIM ULANG EMAIL VERIFIKASI
    // ──────────────────────────────────────────────────────────────

    /**
     * Kirim ulang link verifikasi.
     * Route: POST /email/verification-notification  [throttle:6,1]
     * Nama route: verification.send
     */
    public function sendVerificationEmail(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (empty($user->email)) {
            return back()->withErrors(['profile' => 'Email belum diisi.']);
        }

        if ($user->hasVerifiedEmail()) {
            return back()->with('success', 'Email sudah terverifikasi.');
        }

        if (empty($user->email_verification_token)) {
            $user->email_verification_token = Str::random(60);
            $user->save();
        }

        $this->dispatchVerificationEmail($user);

        return back()->with('success', 'Link verifikasi berhasil dikirim ke ' . $user->email);
    }

    // ──────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ──────────────────────────────────────────────────────────────

    /**
     * Kirim email verifikasi ke user.
     * Di production: aktifkan Mail::to(...)->send(...)
     * Di local: flash link ke session sebagai simulasi.
     */
    private function dispatchVerificationEmail(User $user): void
    {
        $url = route('profile.verify.email', [
            'token' => $user->email_verification_token,
        ]);

        /**
         * Aktifkan jika mail sudah dikonfigurasi di .env
         */
        // Mail::to($user->email)->send(new \App\Mail\VerifyEmailMail($url));

        // Simulasi: tampilkan link di session (local/staging only)
        session()->flash('verification_link', $url);
    }
}