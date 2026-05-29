<?php

namespace App\Models;

use App\Support\CfSchema;
use App\Support\ProjectImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penyakit extends Model
{
    use HasFactory;

    protected $table = 'penyakit';

    protected $fillable = ['kode', 'nama', 'deskripsi', 'gambar'];

    public function gejala()
    {
        $relation = $this->belongsToMany(
            Gejala::class,
            'penyakit_gejala',
            'id_penyakit',
            'id_gejala'
        );

        if (CfSchema::hasSymptomCfColumns()) {
            $relation->withPivot(['mb', 'md']);
        }

        return $relation;
    }

    public function pupuk()
    {
        $relation = $this->belongsToMany(
            Pupuk::class,
            'penyakit_pupuk',
            'id_penyakit',
            'id_pupuk'
        );

        if (CfSchema::hasPupukRuleTable()) {
            $relation->withPivot(['mb', 'md'])->withTimestamps();
        }

        return $relation;
    }

    public function pestisida()
    {
        $relation = $this->belongsToMany(
            Pestisida::class,
            'penyakit_pestisida',
            'id_penyakit',
            'id_pestisida'
        );

        if (CfSchema::hasPestisidaRuleTable()) {
            $relation->withPivot(['mb', 'md'])->withTimestamps();
        }

        return $relation;
    }

    public function rekomendasi()
    {
        return $this->hasMany(Rekomendasi::class, 'id_penyakit');
    }

    public function getGambarUrlAttribute(): ?string
    {
        return ProjectImage::url($this->gambar);
    }
}
