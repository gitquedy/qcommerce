<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

class AuthController extends Controller
{
    public function register(Request $request){

        
        $data = $request->validate([
            'first_name' => 'required|max:55',
            'last_name' => 'required|max:55',
            'phone' => ['nullable','regex:/^(09|\+639)\d{9}$/'],
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed',
        ]);

        $data['password'] = bcrypt($request->password);
        $user = User::create($data);
        $token = $user->updateToken();


        return response()->json(['user' => $user, 'access_token' => $token, 'message' => 'Successfully registered']);
    }

    public function login(Request $request){
        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required',
        ]);

        if(! auth()->attempt($data)){
            return response()->json(['message' => 'Invalid Credentials', 'success' => 0]);
        }
        $token = auth()->user()->updateToken();

        return response()->json(['user' => auth()->user(), 'access_token' => $token, 'success' => 1, 'message' => 'Successfully logged in']);
    }


    public function user(Request $request){
        return response()->json(['user' => $request->user]);
    }
}
