<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class shipper extends Model
{

    protected $fillable = ['numberPlate', 'id'];

    protected $table = 'shippers';
    protected $keyType = 'string';


    public function orders() {
        return $this->hasMany('App\orders', 'idShipper', 'id');
    }

    public function works() {
        return $this->hasMany('App\workLocations', 'idShipper', 'id');
    }

    public function account() {
        return $this->belongsTo('App\User', 'idUser', 'id');
    }

    public function scopeDistrict(Builder $query, $district){
        return $query->whereHas('works', function ($q) use ($district) {
            $q->where('idDistrict', $district);
        });
    }

    public function getWorkLocationsAttribute(){
        return $this->works()->first()->pluck('id');
    }
}
