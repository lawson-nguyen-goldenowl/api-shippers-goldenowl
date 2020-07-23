<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait Mapbox
{
    public $host = "https://api.mapbox.com/directions-matrix/v1/mapbox/cycling/";
    private $token = "pk.eyJ1IjoibGF3c29ubmd1eWVuIiwiYSI6ImNrY29vZ3p0bTBkb2oycG9iaWR0Z3BmaWEifQ.vxBH2svrPtPPi4uXrbLheA";

    function directMatrix($coordinates ){
        $url = $this->host.$coordinates."?annotations=distance&access_token=".$this->token;
        $respond = Http::get($url)->json();
        return $respond;
    }
}