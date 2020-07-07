<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class shipper extends Model
{
    protected $table = 'shippers';
    protected $keyType = 'string';

    public function works() {
        return $this->hasMany('App\works', 'idShipper', 'id');
    }

    public function account() {
        return $this->hasOne('App\User', 'id', 'idUser');
    }
}
