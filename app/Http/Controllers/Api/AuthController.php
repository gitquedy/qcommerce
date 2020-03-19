<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Business;
use Validator;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class AuthController extends Controller
{
    public function register(Request $request){
        $validation = [
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'first_name' => 'required|max:55',
            'last_name' => 'required|max:55',
            'phone' => ['nullable','regex:/^(09|\+639)\d{9}$/'],
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed',
        ];

        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            return ResponseBuilder::asError(422)
                  ->withHttpCode(422)
                  ->withDebugData(['error' => $validator->errors()->toArray()])
                  ->withMessage('Invalid Input')
                  ->build();
        }

        $user = $request->only(['name', 'location','first_name','last_name','phone','email','password']);
        $business = Business::create([
             'name' => $user['name'],
             'location' => $user['location'],
        ]);
        $user['business_id'] = $business->id;
        $user['password'] = bcrypt($request->password);
        $user = User::create($user);
        $user->giveOwnerPermissions();
        $token = $user->updateToken();
        $user = User::find($user->id);
        $data = ['data' => $user, 'access_token' => $token];
        return ResponseBuilder::asSuccess(201)
          ->withData($data)
          ->withMessage('Successfully registered')
          ->build();
    }

    public function login(Request $request){
        $data = (object)[];
        $validation = [
            'email' => 'email|required',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $validation);

        if ($validator->fails()) {
            return ResponseBuilder::asError(422)
                  ->withHttpCode(422)
                  ->withDebugData(['error' => $validator->errors()->toArray()])
                  ->withMessage('Invalid Input')
                  ->build();
        }
        
        $data = $request->only(['email', 'password']);
        if(! auth()->attempt($data)){
            return ResponseBuilder::asError(401)
                  ->withDebugData(['error' => ['email' => 'Invalid Credentials']])
                  ->withMessage('Invalid Credentials')
                  ->build();
        }
        $token = auth()->user()->updateToken();
        $data = ['user' => auth()->user(), 'access_token' => $token];
        return ResponseBuilder::asSuccess(200)
          ->withData($data)
          ->withMessage('Successfully logged in')
          ->build();
    }


    public function user(Request $request){
        return ResponseBuilder::asSuccess(200)
                  ->withData(['user' => $request->user()])
                  ->withMessage('OK')
                  ->build();
    }
}
