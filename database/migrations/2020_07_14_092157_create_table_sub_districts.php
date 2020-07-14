<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSubDistricts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subDistricts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('idDistrict')->unsigned();
            $table->timestamps();
            $table->foreign('idDistrict')->references('id')->on('districts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subDistricts');
    }
}
