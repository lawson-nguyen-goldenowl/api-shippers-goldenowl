<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\api\apiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\permission as Permission;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\places as Places;

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
        $allPlaces = Places::select('id')->get()->pluck('id')->toArray();
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'c_password' => 'required|same:password',
                'number_plate' => 'required|unique:shippers,numberPlate',
                'places' => ['required', Rule::in(array_map('strval', $allPlaces))],
            ]
        );

        if ($validator->fails()) return $this->respondUnauthorized($validator->errors());

        $input = $request->all();
        $input['permission'] = Permission::where('title', 'shipper')->first()->id;
        $input['password'] = bcrypt($input['password']);

        DB::beginTransaction();
        try {
            $account = User::create($input);
            $account->shipper()->create([
                'numberPlate' => $input['number_plate'],
            ])->works()->saveMany($input['places']);
            $success['token'] = $account->createToken('MyApp')->accessToken;
            $respond = [
                'success' => $success
            ];
            return $this->respond($respond);
        } catch (\Exception $error) {
            DB::rollBack();
            return $this->respondWithError($error);
        };
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
