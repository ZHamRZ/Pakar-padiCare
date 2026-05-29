<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailRekomendasiPupuk extends Model
{
    public $timestamps = false;

    protected $table = 'detail_rekomendasi_pupuk';

    protected $fillable = ['id_rekomendasi', 'id_pupuk', 'nilai_vi', 'peringkat'];

    public function rekomendasi()
    {
        return $this->belongsTo(Rekomendasi::class, 'id_rekomendasi');
    }

    public function pupuk()
    {
        return $this->belongsTo(Pupuk::class, 'id_pupuk');
    }
}
