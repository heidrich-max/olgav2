<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduktVariante extends Model
{
    protected $table = 'produkt_varianten';

    protected $fillable = [
        'produkt_id',
        'artikelnummer_full',
        'farbcode',
        'farbe',
        'foto01',
        'foto02',
        'foto03',
        'foto04',
    ];

    public function produkt()
    {
        return $this->belongsTo(Produkt::class, 'produkt_id');
    }
}
