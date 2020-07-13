<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class shipper extends Model
{

    protected $fillable = ['numberPlate', 'id'];

    protected $table = 'shippers';
    protected $keyType = 'string';


    public function orders() {
        return $this->hasMany('App\orders', 'idShipper', 'id');
    }

    public function works() {
        return $this->hasMany('App\works', 'idShipper', 'id');
    }

    public function account() {
        return $this->belongsTo('App\User', 'id', 'idUser');
    }
}
