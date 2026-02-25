<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AngebotAblehnen extends Model
{
    protected $table = 'angebot_ablehnen';
    public $timestamps = false;

    protected $fillable = [
        'grund',
    ];
}
