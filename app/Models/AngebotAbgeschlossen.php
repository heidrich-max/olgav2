<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AngebotAbgeschlossen extends Model
{
    protected $table = 'angebot_abgeschlossen';
    
    // Die Tabelle nutzt 'timestamp' statt standard Laravel Timestamps
    public $timestamps = false;

    protected $fillable = [
        'angebot_id',
        'projekt_id',
        'user_id',
        'grund_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function grund()
    {
        return $this->belongsTo(AngebotAblehnen::class, 'grund_id');
    }
}
