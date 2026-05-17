<?php

namespace App\Models;

use App\Support\CfSchema;
use App\Support\ProjectImage;
use Illuminate\Database\Eloquent\Model;

class Pestisida extends Model
{
    protected $table    = 'pestisida';
    protected $fillable = [
        'kode',
        'nama',
        'jenis',
        'bahan_aktif',
        'kandungan_detail',
        'fungsi',
        'dosis',
        'takaran',
        'efek_penggunaan',
        'cara_aplikasi',
        'jadwal_umur_aplikasi',
        'frekuensi_aplikasi',
        'harga',
        'satuan_harga',
        'gambar',
    ];

    public function detailRekomendasi()
    {
        return $this->hasMany(DetailRekomendasiPestisida::class, 'id_pestisida');
    }

    public function penyakit()
    {
        $relation = $this->belongsToMany(
            Penyakit::class,
            'penyakit_pestisida',
            'id_pestisida',
            'id_penyakit'
        );

        if (CfSchema::hasPestisidaRuleTable()) {
            $relation->withPivot(['mb', 'md'])->withTimestamps();
        }

        return $relation;
    }

    public function getHargaFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    public function getJenisBadgeAttribute(): string
    {
        return match ($this->jenis) {
            'fungisida'   => 'success',
            'bakterisida' => 'info',
            'insektisida' => 'warning',
            'herbisida'   => 'secondary',
            default       => 'primary',
        };
    }

    public function getGambarUrlAttribute(): ?string
    {
        return ProjectImage::url($this->gambar);
    }
}
