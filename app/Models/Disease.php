<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disease extends Model
{
    protected $fillable = [
        'nama_penyakit',
        'kode_icd',
        'kategori',
        'gejala_umum',
        'tindakan',
    ];
}
