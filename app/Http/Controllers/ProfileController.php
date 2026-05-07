<?php

namespace App\Http\Controllers;

use App\Mail\VerifyEmailMail;
use App\Models\User;
use App\Support\ProjectImage;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

    public function update(Request $request): RedirectResponse
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
            'no_telepon' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('users', 'no_telp')->ignore($user->id),
            ],
            'alamat' => 'nullable|string',
            'catatan_profil' => 'nullable|string',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'password_lama' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'no_telepon.unique' => 'Nomor telepon sudah digunakan.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

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

        if ($request->hasFile('foto_profil')) {
            ProjectImage::delete($user->foto_profil);
            $user->foto_profil = ProjectImage::store(
                $request->file('foto_profil'),
                'profil'
            );
        }

        $emailBaru = $validated['email'] ?? null;
        $emailBerubah = $emailBaru !== $user->email;

        $user->fill([
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'no_telp' => $validated['no_telepon'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'catatan_profil' => $validated['catatan_profil'] ?? null,
            'email' => $emailBaru,
        ]);

        if ($emailBerubah) {
            $user->email_verified_at = null;
            $user->email_verification_token = $emailBaru
                ? Str::random(60)
                : null;
        }

        $user->save();

        if ($emailBerubah && $user->email) {
            $this->dispatchVerificationEmail($user);

            return back()->with('success', 'Profil berhasil diperbarui. Link verifikasi dikirim ke email baru.');
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function verifyEmail(EmailVerificationRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $route = $this->profileRouteFor($user);

        if ($user->hasVerifiedEmail()) {
            return redirect()->route($route)
                ->with('success', 'Email sudah diverifikasi sebelumnya.');
        }

        $request->fulfill();
        $user->forceFill(['email_verification_token' => null])->save();

        return redirect()->route($route)
            ->with('success', 'Email berhasil diverifikasi.');
    }

    public function verifyEmailByToken(string $id, string $hash): RedirectResponse
    {
        $user = User::findOrFail($id);
        $route = $this->profileRouteFor($user);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Link verifikasi tidak valid.');
        }

        return $this->markEmailVerified($user, $route);
    }

    public function verifyEmailByRandomToken(string $token): RedirectResponse
    {
        $user = User::where('email_verification_token', $token)->firstOrFail();

        return $this->markEmailVerified($user);
    }

    public function sendVerificationEmail(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (!$user->email) {
            return back()->withErrors(['email' => 'Email belum diisi.']);
        }

        if ($user->hasVerifiedEmail()) {
            return back()->with('success', 'Email sudah terverifikasi.');
        }

        if (!$user->email_verification_token) {
            $user->forceFill([
                'email_verification_token' => Str::random(60),
            ])->save();
        }

        $this->dispatchVerificationEmail($user);

        return back()->with('success', 'Link verifikasi berhasil dikirim ke ' . $user->email);
    }

    private function markEmailVerified(User $user, ?string $route = null): RedirectResponse
    {
        $redirect = $this->verificationRedirectFor($user, $route);

        if ($user->hasVerifiedEmail()) {
            return $redirect->with('success', 'Email sudah diverifikasi sebelumnya.');
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'email_verification_token' => null,
        ])->save();

        event(new Verified($user));

        return $redirect->with('success', 'Email berhasil diverifikasi. Fitur reset password sekarang aktif untuk akun ini.');
    }

    private function dispatchVerificationEmail(User $user): void
    {
        $url = route('profile.verify.email', [
            'token' => $user->email_verification_token,
        ]);

        Mail::to($user->email)->send(new VerifyEmailMail($user, $url));
    }

    private function profileRouteFor(User $user): string
    {
        return $user->isAdmin()
            ? 'admin.profile.edit'
            : 'user.profile.edit';
    }

    private function verificationRedirectFor(User $user, ?string $route = null): RedirectResponse
    {
        if (Auth::check() && Auth::id() === $user->id) {
            return redirect()->route($route ?? $this->profileRouteFor($user));
        }

        return redirect()->route('login');
    }
}
