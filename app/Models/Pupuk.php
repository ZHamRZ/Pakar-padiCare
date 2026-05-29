<?php

namespace App\Models;

use App\Support\CfSchema;
use App\Support\ProjectImage;
use Illuminate\Database\Eloquent\Model;

class Pupuk extends Model
{
    protected $table = 'pupuk';

    protected $fillable = [
        'kode',
        'nama',
        'kandungan',
        'kandungan_detail',
        'fungsi_utama',
        'dosis_per_hektar',
        'satuan_dosis',
        'efek_penggunaan',
        'cara_aplikasi',
        'jadwal_umur_aplikasi',
        'frekuensi_aplikasi',
        'harga_per_unit',
        'satuan_harga_qty',
        'satuan_harga_unit',
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
        $qty = $this->satuan_harga_qty ?? 1;
        $unit = $this->satuan_harga_unit ?? 'kg';

        return 'Rp '.number_format($this->harga_per_unit, 0, ',', '.')." / {$qty} {$unit}";
    }

    public function getGambarUrlAttribute(): ?string
    {
        return ProjectImage::url($this->gambar);
    }
}
