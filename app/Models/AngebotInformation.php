<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AngebotInformation extends Model
{
    protected $table = 'angebot_informationen';
    
    // Die Tabelle nutzt 'timestamp' statt 'created_at'/'updated_at'
    public $timestamps = false;

    protected $fillable = [
        'angebot_id',
        'projekt_id',
        'user_id',
        'information',
    ];

    /**
     * Der Bearbeiter, der diesen Eintrag erstellt hat.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
