<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class, 20)->create()->each(function ($user) {
            $shipper = $user->shipper()->save(factory(App\shipper::class)->make());
            App\places::all()->random(rand(1,3))->each(function ($place) use ($shipper) {
                $work =  new App\works;
                $work->idShipper = $shipper->id;
                $work->idPlaces = $place->id;
                $work->save();
            });
        });
    }
}
