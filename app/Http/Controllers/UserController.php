<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        print json_encode("User");
    }



    public function editProfile() {
        $user = Auth::user();
        // print json_encode($user);die();
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=>"/",'name'=>"User"], ['name'=>"User Settings"]
        ];

        return view('/user/edit_profile', [
            'breadcrumbs' => $breadcrumbs,
            'user' => $user
        ]);
    }

    public function updateProfile(Request $request) {
        $user = Auth::user();
        $request->validate([
            'picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email,'.$user->id,
            'phone' => '',
        ]);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;


        
        if(file_exists(public_path('images/profile/profile-picture/'.$user->picture))){
          unlink(public_path('images/profile/profile-picture/'.$user->picture));
        }
        $pictureName = 'user_'.$user->id.'.'.request()->picture->getClientOriginalExtension();
        $request->picture->move(public_path('images/profile/profile-picture'), $pictureName);
        $user->picture = $pictureName;
        $user->save();

        return back()->with('success','Profile updated successfully.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
