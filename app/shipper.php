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
}
