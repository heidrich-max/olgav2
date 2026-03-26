<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produkt extends Model
{
    use HasFactory;

    protected $table = 'produkte';

    protected $guarded = [];

    protected $casts = [
        'preis' => 'decimal:2',
        'menge_pro_karton' => 'integer',
        'mindestmenge' => 'integer',
        'lieferzeit_ohne_druck_min' => 'integer',
        'lieferzeit_ohne_druck_max' => 'integer',
        'lieferzeit_mit_druck_min' => 'integer',
        'lieferzeit_mit_druck_max' => 'integer',
    ];

    public function varianten()
    {
        return $this->hasMany(ProduktVariante::class, 'produkt_id');
    }
}
