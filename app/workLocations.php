<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class workLocations extends Model
{
    protected $table = 'workLocations';

    public function district() {
        return $this->belongsTo('App\districts', 'id', 'idDistrict');
    }
}