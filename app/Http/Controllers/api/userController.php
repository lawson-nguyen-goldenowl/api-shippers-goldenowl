<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\permission as Permission;
class userController extends Controller
{
    public $successStatus = 200;

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
            $success['token'] = $user->createToken('MyApp')->accessToken;
            $success['userInfo'] = [
                'name' => $user->name
            ];
            return response()->json(
                [
                    'success' => $success,
                ],
                $this->successStatus
            );
        }
        else {
            return response()->json(
                [
                    'error' => 'Unauthorised'
                ], 401);
        }
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'c_password' => 'required|same:password',
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'error' => $validator->errors()
                ], 401);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['permission'] = Permission::where('title','shipper')->first()->id;
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->accessToken;
        $success['userInfo'] = [
            'name' => $user->name,
        ];
        return response()->json(
            [
                'success' => $success
            ],
            $this->successStatus
        );
    }

    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
        $user = Auth::user();

        return response()->json(
            [
                'success' => $user
            ],
            $this->successStatus
        );
    }
}
