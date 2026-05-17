<?php

namespace App\Models;

use App\Support\CfSchema;
use App\Support\ProjectImage;
use Illuminate\Database\Eloquent\Model;

class Pupuk extends Model
{
    protected $table    = 'pupuk';
    protected $fillable = [
        'kode',
        'nama',
        'kandungan',
        'kandungan_detail',
        'fungsi_utama',
        'takaran',
        'efek_penggunaan',
        'cara_aplikasi',
        'jadwal_umur_aplikasi',
        'frekuensi_aplikasi',
        'harga_per_kg',
        'satuan',
        'gambar',
    ];

    public function detailRekomendasi()
    {
        return $this->hasMany(DetailRekomendasiPupuk::class, 'id_pupuk');
    }

    public function penyakit()
    {
        $relation = $this->belongsToMany(
            Penyakit::class,
            'penyakit_pupuk',
            'id_pupuk',
            'id_penyakit'
        );

        if (CfSchema::hasPupukRuleTable()) {
            $relation->withPivot(['mb', 'md'])->withTimestamps();
        }

        return $relation;
    }

    public function getHargaFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_per_kg, 0, ',', '.');
    }

    public function getGambarUrlAttribute(): ?string
    {
        return ProjectImage::url($this->gambar);
    }
}
