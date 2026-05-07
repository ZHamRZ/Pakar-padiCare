<?php

namespace App\Models;

use App\Notifications\CustomResetPassword;
use App\Support\ProjectImage;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * Nama tabel (opsional, tapi kita tulis untuk eksplisit)
     */
    protected $table = 'users';

    /**
     * Field yang boleh diisi
     */
    protected $fillable = [
        'nama',
        'username',
        'email',
        'no_telp',
        'alamat',
        'catatan_profil',
        'foto_profil',
        'password',
        'role',
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke tabel rekomendasi
     */
    public function rekomendasi()
    {
        return $this->hasMany(Rekomendasi::class, 'id_user');
    }

    /**
     * Helper: cek apakah admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function getFotoProfilUrlAttribute(): ?string
    {
        return ProjectImage::url($this->foto_profil);
    }

    public function getDisplayIdentifierAttribute(): string
    {
        return $this->username ?: ($this->email ?: '-');
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
