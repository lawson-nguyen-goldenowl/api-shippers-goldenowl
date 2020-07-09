<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class permission extends Model
{
    protected $table = 'permission';

    public function user() {
        return $this->hasMany('App\User', 'permission', 'id');
    }
}
