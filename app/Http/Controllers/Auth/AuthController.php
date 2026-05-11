<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmailMail;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectAfterAuthenticated(Auth::user());
        }

        return redirect()->to(route('home') . '#login');
    }

    public function login(Request $request)
    {
        // Handle AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ], [
                'username.required' => 'Username wajib diisi.',
                'password.required' => 'Password wajib diisi.',
            ]);

            $user = User::where('username', $request->username)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Username dan password tidak di temukan.'
                ], 401);
            }

            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password salah.'
                ], 401);
            }

            Auth::login($user, $request->boolean('remember'));

            $request->session()->regenerate();

            // Determine redirect URL based on role for AJAX login
            // Always redirect to dashboard based on role, regardless of email verification status
            $redirectUrl = $user->isAdmin() ? route('admin.dashboard') : route('user.dashboard');

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil.',
                'redirect' => $redirectUrl,
                'role' => $user->role
            ]);
        }

        // Handle traditional form submission
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Username dan password tidak di temukan.');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Password salah.');
        }

        if (!Auth::login($user, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Username atau password salah.');
        }

        $request->session()->regenerate();

        return $this->redirectAfterAuthenticated(Auth::user());
    }

    public function adminLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username admin wajib diisi.',
            'password.required' => 'Password admin wajib diisi.',
        ]);

        $credentials = $request->only('username', 'password');

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Username admin atau password salah.');
        }

        $request->session()->regenerate();

        if (!Auth::user()->isAdmin()) {
            Auth::logout();

            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Form login admin hanya untuk akun admin.');
        }

        return $this->redirectAfterAuthenticated(Auth::user());
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectAfterAuthenticated(Auth::user());
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah di gunakan',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $user = User::create([
            'nama' => $request->username,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'petani',
        ]);

        Auth::login($user);

        return redirect()
            ->route('user.profile.edit')
            ->with('success', 'Registrasi berhasil. Silakan isi dan verifikasi email di profil agar fitur lupa password aktif.');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
        }

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('home')->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Tampilkan form lupa password
     */
    public function showForgotPassword()
    {
        if (Auth::check()) {
            return $this->redirectAfterAuthenticated(Auth::user());
        }

        return view('auth.forgot-password');
    }

    /**
     * Proses kirim link reset password ke email
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Email tidak terdaftar di sistem kami.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email tidak terdaftar di sistem kami.']);
        }

        if (!$user->hasVerifiedEmail()) {
            if (!$user->email_verification_token) {
                $user->forceFill([
                    'email_verification_token' => Str::random(60),
                ])->save();
            }

            Mail::to($user->email)->send(new VerifyEmailMail(
                $user,
                route('profile.verify.email', ['token' => $user->email_verification_token])
            ));

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email belum diverifikasi. Kami mengirim link verifikasi ke email Anda. Verifikasi dulu, lalu minta reset password lagi.']);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()
                ->with('success', 'Email reset password telah dikirim. Klik tombol Reset Password di email untuk membuka halaman buat password baru.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }

    /**
     * Tampilkan form reset password
     */
    public function showResetForm(Request $request, $token)
    {
        if (Auth::check()) {
            return $this->redirectAfterAuthenticated(Auth::user());
        }

        $user = $request->email
            ? User::where('email', $request->email)->first()
            : null;

        if (!$user || !$user->hasVerifiedEmail()) {
            return redirect()
                ->route('password.request')
                ->withErrors(['email' => 'Email harus diverifikasi terlebih dahulu sebelum membuat password baru.']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Proses reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'token.required' => 'Token reset password diperlukan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Email tidak terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->hasVerifiedEmail()) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email belum diverifikasi sehingga password tidak dapat direset.']);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('success', 'Password berhasil direset. Silakan login dengan password baru.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }

    private function redirectAfterAuthenticated(User $user)
    {
        if ($user->email && $user->hasVerifiedEmail()) {
            return $user->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('user.dashboard');
        }

        return redirect()
            ->route($user->isAdmin() ? 'admin.profile.edit' : 'user.profile.edit')
            ->with('error', $user->email
                ? 'Silakan verifikasi email terlebih dahulu. Link verifikasi dapat dikirim ulang dari halaman profil.'
                : 'Silakan lengkapi dan verifikasi email terlebih dahulu agar fitur reset password aktif.');
    }

}
