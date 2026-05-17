<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rekomendasi extends Model
{
    protected $table    = 'rekomendasi';
    protected $fillable = ['id_pengguna', 'id_penyakit', 'tanggal', 'preferensi_label', 'preferensi_pengguna'];
    protected $dates    = ['tanggal'];
    protected $casts    = ['preferensi_pengguna' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class,     'id_pengguna');
    }
    public function penyakit()
    {
        return $this->belongsTo(Penyakit::class, 'id_penyakit');
    }

    public function detailPupuk()
    {
        return $this->hasMany(DetailRekomendasiPupuk::class, 'id_rekomendasi')
            ->orderBy('peringkat');
    }

    public function detailPestisida()
    {
        return $this->hasMany(DetailRekomendasiPestisida::class, 'id_rekomendasi')
            ->orderBy('peringkat');
    }
}
