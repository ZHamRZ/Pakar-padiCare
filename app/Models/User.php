<?php

namespace App\Models;

use App\Notifications\CustomResetPassword;
use App\Support\ProjectImage;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

// Hapus implements MustVerifyEmail jika tidak menggunakan verifikasi bawaan Laravel
class User extends Authenticatable
{
    use HasFactory, MustVerifyEmail, Notifiable;

    /**
     * Nama tabel
     */
    protected $table = 'pengguna';

    /**
     * Field yang boleh diisi
     */
    protected $fillable = [
        'nama',
        'username',
        'alamat',
        'catatan_profil',
        'foto_profil',
        'password',
        'role',

        // Tambahan untuk email verification
        'email',
        'email_verified_at',
        'email_verification_token',
    ];

    /**
     * Field yang disembunyikan
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting attribute
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke tabel rekomendasi
     */
    public function rekomendasi()
    {
        return $this->hasMany(Rekomendasi::class, 'id_pengguna');
    }

    /**
     * Helper: cek apakah admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Helper: cek apakah email sudah diverifikasi
     */
    public function isEmailVerified(): bool
    {
        return ! is_null($this->email_verified_at);
    }

    /**
     * Helper: generate token verifikasi email
     */
    public function generateVerificationToken()
    {
        $token = Str::random(60);

        $this->update([
            'email_verification_token' => $token,
            'email_verified_at' => null,
        ]);

        return $token;
    }

    /**
     * Accessor URL foto profil
     */
    public function getFotoProfilUrlAttribute(): ?string
    {
        return ProjectImage::url($this->foto_profil);
    }

    /**
     * Identifier tampilan user
     */
    public function getDisplayIdentifierAttribute(): string
    {
        return $this->username ?: '-';
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }
}
