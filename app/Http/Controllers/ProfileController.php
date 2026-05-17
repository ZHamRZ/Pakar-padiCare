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
use Illuminate\Support\Facades\RateLimiter;
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

        $request->merge([
            'email' => $this->normalizeEmail($request->input('email')),
        ]);

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('pengguna', 'username')->ignore($user->id),
            ],
            'email' => [
                'bail',
                'nullable',
                'string',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (!$this->isValidEmailFormat((string) $value)) {
                        $fail('Email harus valid dan domainnya benar, misalnya nama@gmail.com, nama@yahoo.com, atau nama@domain.co.id.');
                    }
                },
                Rule::unique('pengguna', 'email')->ignore($user->id),
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
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah digunakan.',
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

    /**
     * Handle AJAX photo crop upload from profile page
     */
    public function uploadCroppedPhoto(Request $request): \Illuminate\Http\JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $request->validate([
                'foto_profil' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            ], [
                'foto_profil.required' => 'Foto profil wajib diupload.',
                'foto_profil.image' => 'File harus berupa gambar.',
                'foto_profil.mimes' => 'Format gambar harus JPG, PNG, atau WebP.',
                'foto_profil.max' => 'Ukuran gambar maksimal 2MB.',
            ]);

            if ($request->hasFile('foto_profil')) {
                // Delete old photo
                ProjectImage::delete($user->foto_profil);
                
                // Store new cropped photo
                $user->foto_profil = ProjectImage::store(
                    $request->file('foto_profil'),
                    'profil'
                );
                
                $user->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Foto profil berhasil diperbarui.',
                    'foto_url' => $user->foto_profil_url,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file yang diupload.',
            ], 422);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupload foto.',
                'error' => $e->getMessage(),
            ], 500);
        }
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

        $rateLimitKey = 'verification-email:' . $user->id;

        if (RateLimiter::tooManyAttempts($rateLimitKey, 1)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);

            return back()
                ->withErrors([
                    'email' => 'Tunggu ' . $seconds . ' detik sebelum mengirim ulang email verifikasi.',
                ])
                ->with('verification_resend_available_at', now()->addSeconds($seconds)->timestamp);
        }

        if (!$user->email_verification_token) {
            $user->forceFill([
                'email_verification_token' => Str::random(60),
            ])->save();
        }

        $this->dispatchVerificationEmail($user);
        RateLimiter::hit($rateLimitKey, 30);

        return back()
            ->with('success', 'Link verifikasi berhasil dikirim ke ' . $user->email)
            ->with('verification_resend_available_at', now()->addSeconds(30)->timestamp);
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

    private function normalizeEmail(mixed $email): ?string
    {
        $email = strtolower(trim((string) $email));

        return $email === '' ? null : $email;
    }

    private function isValidEmailFormat(string $email): bool
    {
        if ($email === '' || strlen($email) > 255) {
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (!preg_match('/^(?!.*\.\.)[a-z0-9](?:[a-z0-9._%+\-]{0,62}[a-z0-9])?@(?:[a-z0-9](?:[a-z0-9\-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/', $email)) {
            return false;
        }

        [$localPart, $domain] = explode('@', $email, 2);

        if (strlen($localPart) > 64 || strlen($domain) > 253) {
            return false;
        }

        foreach (explode('.', $domain) as $label) {
            if ($label === '' || strlen($label) > 63 || str_starts_with($label, '-') || str_ends_with($label, '-')) {
                return false;
            }
        }

        if (!$this->canReceiveEmail($domain)) {
            return false;
        }

        return true;
    }

    private function canReceiveEmail(string $domain): bool
    {
        $trustedDomains = [
            'gmail.com',
            'googlemail.com',
            'yahoo.com',
            'yahoo.co.id',
            'outlook.com',
            'hotmail.com',
            'live.com',
            'icloud.com',
            'me.com',
            'proton.me',
            'protonmail.com',
        ];

        if (in_array($domain, $trustedDomains, true)) {
            return true;
        }

        if (!function_exists('checkdnsrr')) {
            return false;
        }

        return checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A') || checkdnsrr($domain, 'AAAA');
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
