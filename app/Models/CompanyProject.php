<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProject extends Model
{
    protected $table = 'auftrag_projekt_firma';
    
    public $timestamps = false;

    protected $fillable = [
        'name',
        'firma_id',
        'name_kuerzel',
        'bg',
        'co',
        'strasse',
        'plz',
        'ort',
        'telefon',
        'email',
        'inhaber',
        'ust_id',
        'handelsregister',
    ];
}
