<?php

namespace App\Models;

use App\Support\ProjectImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Gejala extends Model
{
    protected $table = 'gejala';

    protected $fillable = ['kode', 'nama_gejala', 'gambar'];

    public function penyakit()
    {
        return $this->belongsToMany(
            Penyakit::class,
            'penyakit_gejala',
            'id_gejala',
            'id_penyakit'
        );
    }

    public function getGambarUrlAttribute(): ?string
    {
        if (! self::supportsImages()) {
            return null;
        }

        return ProjectImage::url($this->gambar);
    }

    public static function supportsImages(): bool
    {
        static $supportsImages;

        if ($supportsImages === null) {
            $supportsImages = Schema::hasColumn('gejala', 'gambar');
        }

        return $supportsImages;
    }

    public static function selectableColumns(array $columns = ['id', 'kode', 'nama_gejala']): array
    {
        if (self::supportsImages() && ! in_array('gambar', $columns, true)) {
            $columns[] = 'gambar';
        }

        return $columns;
    }
}
