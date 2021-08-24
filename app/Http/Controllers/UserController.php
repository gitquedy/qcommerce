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
use App\Package;
use App\Shop;
use App\ShopPermission;
use App\WarehousePermission;
use App\Warehouse;
use App\Billing;
use App\Company;

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

    public function updateProfile(Request $request) {
      $user = $request->user();
      $validator = Validator::make($request->all(), [
            'picture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email,'.$user->id,
            'phone' => '',
          ]);

        if ($validator->fails()) {
          return response()->json(['error' => $validator->errors()]);
        }
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($request->hasFile('picture')) {
           if(file_exists(public_path('images/profile/profile-picture/'.$user->picture))){
          unlink(public_path('images/profile/profile-picture/'.$user->picture));
        }
        $pictureName = 'user_'.$user->id.'.'.request()->picture->getClientOriginalExtension();
        $request->picture->move(public_path('images/profile/profile-picture'), $pictureName);
        $user->picture = $pictureName;
        
        }
        $user->save();
        $output = ['success' => 1,
                        'msg' => 'User updated successfully',
                        'redirect' => action('UserController@settings')
                  ];
        return response()->json($output);
    }

    public function updatePassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'old_password' => ['required', new MatchOldPassword],
            'password' => ['required', 'string', 'min:8'],
            'confirm_password' => ['same:password']
          ]);

        if ($validator->fails()) {
          return response()->json(['error' => $validator->errors()]);
        }
        $request->user()->update(['password'=> Hash::make($request->password)]);

        $output = ['success' => 1,
                        'msg' => 'User updated successfully',
                  ];
        return response()->json($output);
    }
    
    public function updateCompany(Request $request) {
      $user = $request->user();
      $request->validate([
            'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name' => 'required',
            'address' => 'required',
            'vat_tin_no' => 'required',
            'phone' => '',
          ]);

        $company = $user->business->company;
        $company->name = $request->name;
        $company->address = $request->address;
        $company->vat_tin_no = $request->vat_tin_no;
        $company->phone_no = $request->phone_no;

        if ($request->hasFile('logo')) {
          if(file_exists(public_path('images/profile/company-logo/'.$company->logo))){
            unlink(public_path('images/profile/company-logo/'.$company->logo));
          }

          $logoName = 'business_'.$company->business_id.'.'.request()->logo->getClientOriginalExtension();
          $request->logo->move(public_path('images/profile/company-logo'), $logoName);
          $company->logo = $logoName;
        }

        $company->save();
        $output = ['success' => 1,
                        'msg' => 'Company details updated successfully',
                        'redirect' => action('UserController@settings')
                  ];
        return redirect()->action('UserController@settings')->with('status', $output['msg']);
    }

    public function createCompany(Request $request) {
      $user = $request->user();
      $request->validate([
            'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name' => 'required',
            'address' => 'required',
            'vat_tin_no' => 'required',
            'phone' => '',
          ]);

        $company = new Company;
        $company->business_id = $user->business_id;
        $company->name = $request->name;
        $company->address = $request->address;
        $company->vat_tin_no = $request->vat_tin_no;
        $company->phone_no = $request->phone_no;

        if ($request->hasFile('logo')) {
          $logoName = 'business_'.$company->business_id.'.'.request()->logo->getClientOriginalExtension();
          $request->logo->move(public_path('images/profile/company-logo'), $logoName);
          $company->logo = $logoName;
        }

        $company->save();
        $output = ['success' => 1,
                        'msg' => 'Company details saved successfully',
                        'redirect' => action('UserController@settings')
                  ];
        return redirect()->action('UserController@settings')->with('status', $output['msg']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', User::class);
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('UserController@index'), 'name'=>"Users List"], ['name'=>"Add User"]
        ];

        $shops = $request->user()->business->shops->where('active', '!=', 0)->chunk(3);

        $warehouses = $request->user()->business->warehouse->where('status', 1)->chunk(3);
        return view('user.create', compact('breadcrumbs', 'shops', 'warehouses'));
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

        $permissions = collect($request->permissions);
        if($permissions->contains('adjustment.manage')){
          if(collect($request->warehouses)->count() == 0){
            return response()->json(['msg' => 'Kindly check atleast one warehouse in order use adjustment module.' ,'error' => []]);
          }
        }
        if($permissions->contains('sales.manage')){
          if(collect($request->warehouses)->count() == 0){
            return response()->json(['msg' => 'Kindly check atleast one warehouse in order use sales module.' ,'error' => []]);
          }
        }
        if($permissions->contains('transfer.manage')){
          if(collect($request->warehouses)->count() <= 1){
            return response()->json(['msg' => 'Kindly check atleast two warehouse in order use transfer module.' ,'error' => []]);
          }
        }
        if($permissions->contains('expense.manage')){
          if(collect($request->warehouses)->count() <= 0){
            return response()->json(['msg' => 'Kindly check atleast one warehouse in order use expense module.' ,'error' => []]);
          }
        }
        if($permissions->contains('purchase.manage')){
          if(collect($request->warehouses)->count() <= 0){
            return response()->json(['msg' => 'Kindly check atleast one warehouse in order use purchase module.' ,'error' => []]);
          }
        }
        try {
            $data = $request->all();
            // return response()->json($data);
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

            $permissions = $request->permissions;

             if($request->has('shops')){
              $permissions[] = 'shop.manage';
              $shops = Shop::whereIn('id', $request->shops)->get();
              foreach($shops as $shop){
                ShopPermission::create([
                  'shop_id' => $shop->id,
                  'user_id' => $user->id
                ]);
              }
            }

            if($request->has('warehouses')){
              $permissions[] = 'warehouse.manage';
              $warehouses = Warehouse::whereIn('id', $request->warehouses)->get();
              foreach($warehouses as $warehouse){
                WarehousePermission::create([
                  'warehouse_id' => $warehouse->id,
                  'user_id' => $user->id
                ]);
              }
            }
           
             if($request->has('permissions')){
                $permissions = Permission::whereIn('name', $permissions)->get();
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
        $this->authorize('edit', $user);

        $shops = $request->user()->business->shops->where('active', '!=', 0)->chunk(3);

        $warehouses = $request->user()->business->warehouse->where('status', 1)->chunk(3);

        return view('user.edit', compact('user', 'shops', 'warehouses'));
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

        $permissions = collect($request->permissions);
        if($permissions->contains('adjustment.manage')){
          if(collect($request->warehouses)->count() == 0){
            return response()->json(['msg' => 'Kindly check atleast one warehouse in order to use adjustment module.' ,'error' => []]);
          }
        }
        if($permissions->contains('sales.manage')){
          if(collect($request->warehouses)->count() == 0){
            return response()->json(['msg' => 'Kindly check atleast one warehouse in order to use sales module.' ,'error' => []]);
          }
        }
        if($permissions->contains('transfer.manage')){
          if(collect($request->warehouses)->count() <= 1){
            return response()->json(['msg' => 'Kindly check atleast two warehouse in order to use transfer module.' ,'error' => []]);
          }
        }
        if($permissions->contains('expense.manage')){
          if(collect($request->warehouses)->count() <= 0){
            return response()->json(['msg' => 'Kindly check atleast one warehouse in order use expense module.' ,'error' => []]);
          }
        }
        if($permissions->contains('purchase.manage')){
          if(collect($request->warehouses)->count() <= 0){
            return response()->json(['msg' => 'Kindly check atleast one warehouse in order use purchase module.' ,'error' => []]);
          }
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

            ShopPermission::where('user_id', $user->id)->delete();
            WarehousePermission::where('user_id', $user->id)->delete();

            $permissions = $request->permissions;

            if($request->has('shops')){
              $permissions[] = 'shop.manage';
              $shops = Shop::whereIn('id', $request->shops)->get();
              foreach($shops as $shop){
                ShopPermission::create([
                  'shop_id' => $shop->id,
                  'user_id' => $user->id
                ]);
              }
            }

            if($request->has('warehouses')){
              $permissions[] = 'warehouse.manage';
              $warehouses = Warehouse::whereIn('id', $request->warehouses)->get();
              foreach($warehouses as $warehouse){
                WarehousePermission::create([
                  'warehouse_id' => $warehouse->id,
                  'user_id' => $user->id
                ]);
              }
            }

            $remove_permissions = User::ownerPermissions();
            $user->revokePermissionTo($remove_permissions);
             if($request->has('permissions')){
                $permissions = Permission::whereIn('name', $permissions)->get();
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

    public function settings(Request $request){
      return view('user.settings');
    }
}
