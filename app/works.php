<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class works extends Model
{
    protected $fillable = ['idPlaces'];
    protected $table = 'works';
    protected $primaryKey = null;
    public $incrementing = false;
}
