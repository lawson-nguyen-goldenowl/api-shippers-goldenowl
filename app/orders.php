<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class orders extends Model
{
    protected $guarded = [];

    public function shipper() {
        return $this->belongsTo('App\shipper', 'id', 'idShipper');
    }
}
