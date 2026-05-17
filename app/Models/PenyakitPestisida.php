<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenyakitPestisida extends Model
{
    protected $table = 'penyakit_pestisida';

    protected $fillable = [
        'id_penyakit',
        'id_pestisida',
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

    public function pestisida()
    {
        return $this->belongsTo(Pestisida::class, 'id_pestisida');
    }
}
