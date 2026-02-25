<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AngebotAblehnen extends Model
{
    protected $table = 'angebot_ablehnen';
    public $timestamps = false; [L12]

    protected $fillable = [
        'grund',
    ];
}
