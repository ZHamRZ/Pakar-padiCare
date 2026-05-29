<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenyakitPupuk extends Model
{
    protected $table = 'penyakit_pupuk';

    protected $fillable = [
        'id_penyakit',
        'id_pupuk',
        'mb',
        'md',
    ];

    protected $casts = [
        'mb' => 'decimal:3',
        'md' => 'decimal:3',
    ];

    public function penyakit()
    {
        return $this->belongsTo(Penyakit::class, 'id_penyakit');
    }

    public function pupuk()
    {
        return $this->belongsTo(Pupuk::class, 'id_pupuk');
    }
}
