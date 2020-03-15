<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Rules\MatchOldPassword;
use Auth;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use DB;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('UserController@index'), 'name'=>"Users List"], ['name'=>"Users"]
        ];
        if ( request()->ajax()) {
           $user = User::where('business_id', $request->user()->business_id)->orderBy('updated_at', 'desc');
            return Datatables::eloquent($user)
            ->addColumn('nameAndImgDisplay', function(User $user) {
                return $user->getNameAndImgDisplay();
            })
            ->addColumn('statusDisplay', function(User $user) {
                return $user->getStatusDisplay();
            })
            ->addColumn('action', function(User $user) {
                $disabled = $user->role == 'Owner' ? 'disabled' : '';
                    $actions = '<div class="btn-group dropup mr-1 mb-1">
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                    Action<span class="sr-only">Toggle Dropdown</span></button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item '. $disabled .'" href="'. action('UserController@edit', $user->id) .'"><i class="fa fa-edit aria-hidden="true""></i> Edit</a>
                        <a class="dropdown-item modal_button '. $disabled .'" href="#" data-href="'. action('UserController@delete', $user->id).'" ><i class="fa fa-trash aria-hidden="true""></i> Delete</a>
                    </div></div>';
                    return $actions;
             })
            ->rawColumns(['nameAndImgDisplay', 'action', 'statusDisplay'])
            ->make(true);
        }
        return view('user.index', [
            'breadcrumbs' => $breadcrumbs,
        ]);
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

    public function changePassword() {
        $user = Auth::user();
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=>"/",'name'=>"User"], ['name'=>"Change Password"]
        ];

        return view('/user/change_password', [
            'breadcrumbs' => $breadcrumbs,
            'user' => $user
        ]);
    }

    public function updatePassword(Request $request) {
        $request->validate([
            'old_password' => ['required', new MatchOldPassword],
            'password' => ['required', 'string', 'min:8'],
            'confirm_password' => ['same:password'],
        ]);
        

        User::find(auth()->user()->id)->update(['password'=> Hash::make($request->password)]);
        return redirect()->back()->with('success', 'Password Changed successfully!');
        // dd('Password change successfully.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('UserController@index'), 'name'=>"Users List"], ['name'=>"Add User"]
        ];
        return view('user.create', compact('breadcrumbs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'picture' => ['mimes:jpeg,png'],
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        try {
            $data = $request->all();
            DB::beginTransaction();
            $data['business_id'] = $request->user()->business_id;

            if ($request->hasFile('picture')) {
                $image = $request->file('picture');
                $image_name = sha1(time()) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/profile/profile-picture/') , $image_name);

                $data['picture'] = $image_name;
            }


            $data['password'] = Hash::make($data['password']);
            $data['role'] = 'Staff';
            $user = User::create($data);
           
             if($request->has('permissions')){
                $permissions = Permission::whereIn('name', $request->permissions)->get();
                $user->givePermissionTo($permissions);
            }
            $output = ['success' => 1,
                'msg' => 'User added successfully!',
                'redirect' => action('UserController@index')
            ];
            DB::commit();
          
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
             DB::rollBack();
        }
        return response()->json($output);
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
    public function edit(User $user, Request $request)
    {
        if($user->business_id != $request->user()->business_id){
          abort(401, 'You don\'t have access to edit this user');
        }

        return view('user.edit', compact('user'));
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
      if($user->business_id != $request->user()->business_id){
          abort(401, 'You don\'t have access to edit this user');
      }
      // 'unique:users'
      // 
      $validator = Validator::make($request->all(),[
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable','string', 'min:8', 'confirmed'],
            'picture' => ['mimes:jpeg,png'],
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors','error' => $validator->errors()]);
        }
        try {
            $data = $request->only(['first_name', 'last_name', 'email']);
            DB::beginTransaction();
            $data['business_id'] = $request->user()->business_id;

            if ($request->hasFile('picture')) {
                $image = $request->file('picture');
                $image_name = sha1(time()) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/profile/profile-picture/') , $image_name);
                $data['picture'] = $image_name;
            }
            if($request->password != null){
              $data['password'] = Hash::make($request->password);
            }
            $permissions = User::ownerPermissions();
            $user->revokePermissionTo($permissions);
             if($request->has('permissions')){
                $permissions = Permission::whereIn('name', $request->permissions)->get();
                $user->givePermissionTo($permissions);
            }
            $user = $user->update($data);

            $output = ['success' => 1,
                'msg' => 'User updated successfully!',
                'redirect' => action('UserController@index')
            ];
            DB::commit();
          
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
             DB::rollBack();
        }
        return response()->json($output);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user, Request $request)
    {
      if($user->business_id != $request->user()->business_id){
          abort(401, 'You don\'t have access to edit this user');
      }
        try {
            DB::beginTransaction();
            $user->delete();
            DB::commit();
            $output = ['success' => 1,
                        'msg' => 'User successfully deleted!'
                    ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
             DB::rollBack();
        }
        return response()->json($output);
    }

    public function delete(User $user, Request $request){
      if($user->business_id != $request->user()->business_id){
          abort(401, 'You don\'t have access to edit this user');
      }
        $action = action('UserController@destroy', $user->id);
        $title = 'user ' . $user->fullName();
        return view('layouts.delete', compact('action' , 'title'));
    }
}
