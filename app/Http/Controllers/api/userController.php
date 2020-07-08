<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\api\apiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\permission as Permission;

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
                'places' => 'required',
            ]
        );

        if ($validator->fails()) return $this->respondUnauthorized($validator->errors());
        // Create new account
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['permission'] = Permission::where('title', 'shipper')->first()->id;
        $user = User::create($input);
        // Create new shipper matching with the account
        $user->shipper()->create([$input['number_plate']])->works()->saveMany($input['places']);
        $success['token'] = $user->createToken('MyApp')->accessToken;
        $respond = [
            'success' => $success
        ];
        return $this->respond($respond);
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
