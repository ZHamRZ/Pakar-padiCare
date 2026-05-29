<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailRekomendasiPestisida extends Model
{
    public $timestamps = false;

    protected $table = 'detail_rekomendasi_pestisida';

    protected $fillable = ['id_rekomendasi', 'id_pestisida', 'nilai_vi', 'peringkat'];

    public function rekomendasi()
    {
        return $this->belongsTo(Rekomendasi::class, 'id_rekomendasi');
    }

    public function pestisida()
    {
        return $this->belongsTo(Pestisida::class, 'id_pestisida');
    }
}
