<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    protected $table = 'kriteria';

    protected $fillable = ['kode', 'nama', 'jenis', 'bobot', 'keterangan'];
}
