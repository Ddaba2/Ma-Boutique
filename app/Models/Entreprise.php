<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
    protected $fillable = [
        'nom',
        'nif',
        'adresse',
        'telephone',
        'email',
        'logo',
        'site_web',
    ];
}
