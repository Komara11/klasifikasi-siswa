<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    use HasFactory;

    protected $fillable = [
        'luas_tanah',
        'luas_bangunan',
        'kamar_tidur',
        'kamar_mandi',
        'kecamatan',
        'jarak_kota',
        'kondisi',
        'garasi',
        'taman',
        'carport',
        'harga_aktual',
    ];
}
