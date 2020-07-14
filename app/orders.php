<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class orders extends Model
{
    protected $guarded = [];

    public function shipper() {
        return $this->belongsTo('App\shipper', 'id', 'idShipper');
    }

    public function scopeDistrict($query, $request) {
        if ($request->has('district')) {
            $query->where('idDistrict', $request->district);
        }
        return $query;
    }
}
