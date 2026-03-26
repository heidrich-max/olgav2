<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduktPreis extends Model
{
    protected $table = 'produkt_preise';
    
    protected $fillable = [
        'produkt_variante_id',
        'tier_number',
        'quantity',
        'base_price',
        'profit_without_print',
        'profit_print'
    ];

    public function variante()
    {
        return $this->belongsTo(ProduktVariante::class, 'produkt_variante_id');
    }
}
