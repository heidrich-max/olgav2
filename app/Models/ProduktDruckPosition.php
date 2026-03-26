<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduktDruckPosition extends Model
{
    use HasFactory;

    protected $table = 'produkt_druck_positionen';

    protected $fillable = [
        'produkt_variante_id',
        'position_name',
        'print_size',
        'techniques'
    ];

    protected $casts = [
        'techniques' => 'array'
    ];

    public function variante()
    {
        return $this->belongsTo(ProduktVariante::class, 'produkt_variante_id');
    }
}
