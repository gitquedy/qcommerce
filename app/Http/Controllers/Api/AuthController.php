<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Validator;

class AuthController extends Controller
{
    public function register(Request $request){

        $data = (object)[];
        $validation = [
            'first_name' => 'required|max:55',
            'last_name' => 'required|max:55',
            'phone' => ['nullable','regex:/^(09|\+639)\d{9}$/'],
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed',
        ];

        $validator = Validator::make($request->all(), $validation);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->toArray(), 'success' => 0, 'data' => $data, 'message' => 'Input error']);
        }

        $user = $request->only(['first_name','last_name','phone','email','password']);

        $user['password'] = bcrypt($request->password);
        $user = User::create($user);
        $token = $user->updateToken();

        return response()->json(['data' => $user, 'access_token' => $token, 'message' => 'Successfully registered', 'success' => 1]);
    }

    public function login(Request $request){
        $validation = [
            'email' => 'email|required',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $validation);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->toArray(), 'success' => 0]);
        }

        $data = $request->only(['email', 'password']);

        if(! auth()->attempt($data)){
            return response()->json(['message' => 'Invalid Credentials', 'success' => 0]);
        }

        $token = auth()->user()->updateToken();

        return response()->json(['data' => auth()->user(), 'access_token' => $token, 'success' => 1, 'message' => 'Successfully logged in']);
    }


    public function user(Request $request){
        return response()->json(['user' => $request->user, 'success' => 1]);
    }
}
