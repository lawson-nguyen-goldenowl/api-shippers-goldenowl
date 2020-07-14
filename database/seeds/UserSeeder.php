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
        factory(App\User::class, 5)->create()->each(function ($user) {
            $shipper = $user->shipper()->save(factory(App\shipper::class)->make());
            App\districts::all()->random(rand(1,3))->each(function ($dstrict) use ($shipper) {
                $work =  new App\workLocations();
                $work->idShipper = $shipper->id;
                $work->idDistrict = $dstrict->id;
                $work->save();
            });
        });
    }
}
