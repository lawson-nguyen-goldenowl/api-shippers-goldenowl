<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\api\apiController;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\permission as Permission;
use Illuminate\Support\Facades\DB;
use App\shipper;
use Illuminate\Support\Str;

class userController extends apiController
{

    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        if (Auth::attempt(
            [
                'email' => request('email'),
                'password' => request('password')
            ]
        )) {
            $user = Auth::user();
            $data['token'] = $user->createToken('MyApp')->accessToken;
            $respond = [
                'success' => $data
            ];
            return $this->respond($respond);
        } else {
            return $this->respondUnauthorized();
        }
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'c_password' => 'required|same:password',
                'number_plate' => 'required|unique:shippers,numberPlate',
                'places' => 'required'
            ]
        );

        if ($validator->fails()) return $this->respondBadRequest($validator->errors());

        $input = $request->all();
        $input['permission'] = Permission::where('title', 'shipper')->first()->id;
        $input['password'] = bcrypt($input['password']);

        DB::beginTransaction();
        try {
            $account = User::create($input);
            $idShipper = Str::random(8);
            while (shipper::find($idShipper)) $idShipper = Str::random(8);
            $places = array();
            foreach ($input['places'] as $key => $value) {
                $places[] = ['idPlaces' => $value];
            };
            $account->shipper()->create([
                'id' => $idShipper,
                'numberPlate' => $input['number_plate']
            ])->works()->createMany($places);
            $account->save();
            $success['token'] = $account->createToken('MyApp')->accessToken;
            $respond = [
                'success' => $success
            ];
            DB::commit();
            return $this->respond($respond);
        } catch (\Exception $err) {
            DB::rollback();
            abort(500, $err);
        }
    }

    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
        $user = Auth::user();
        unset($user->id);
        return $this->respond($user);
    }
}
