<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableWorkLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workLocations', function (Blueprint $table) {
            $table->id();
            $table->string('idShipper');
            $table->integer('idDistrict')->unsigned();
            $table->timestamps();
            $table->foreign('idShipper')->references('id')->on('shippers')->onDelete('cascade');
            $table->foreign('idDistrict')->references('id')->on('districts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workLocations');
    }
}
