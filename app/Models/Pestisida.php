<?php

namespace App\Models;

use App\Support\CfSchema;
use App\Support\ProjectImage;
use Illuminate\Database\Eloquent\Model;

class Pestisida extends Model
{
    protected $table = 'pestisida';

    protected $fillable = [
        'kode',
        'nama',
        'jenis',
        'bahan_aktif',
        'kandungan_detail',
        'fungsi',
        'dosis',
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
        $qty = $this->satuan_harga_qty ?? 100;
        $unit = $this->satuan_harga_unit ?? 'ml';

        return 'Rp '.number_format($this->harga_per_unit, 0, ',', '.')." / {$qty} {$unit}";
    }

    public function getJenisBadgeAttribute(): string
    {
        return match ($this->jenis) {
            'fungisida' => 'success',
            'bakterisida' => 'info',
            'insektisida' => 'warning',
            'herbisida' => 'secondary',
            default => 'primary',
        };
    }

    public function getGambarUrlAttribute(): ?string
    {
        return ProjectImage::url($this->gambar);
    }
}
