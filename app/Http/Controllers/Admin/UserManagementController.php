<?php

namespace App\Http\Controllers\Admin;

use App\Business;
use App\Http\Controllers\Controller;
use App\Package;
use App\Products;
use App\Rules\MatchOldPassword;
use App\Shop;
use App\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Validator;
use Yajra\DataTables\Facades\DataTables;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('Admin\UserManagementController@index'), 'name'=>"Users List"], ['name'=>"Users"]
        ];
        if (request()->ajax()) {
           $user = User::with('business')->orderBy('updated_at', 'desc');
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
                    <a class="dropdown-item" href="'. action('Admin\UserManagementController@edit', $user->id) .'"><i class="fa fa-edit" aria-hidden="true""></i> Edit</a>
                    <a class="dropdown-item" href="'. route('user.shops', $user) .'"><i class="feather icon-shopping-bag" aria-hidden="true""></i> Shop List</a>
                    <a class="dropdown-item modal_button '.$disabled.'" href="#" data-href="'. action('Admin\UserManagementController@delete', $user->id).'" ><i class="fa fa-trash aria-hidden="true""></i> Delete</a>
                </div></div>';
                return $actions;
             })
            ->rawColumns(['nameAndImgDisplay', 'action', 'statusDisplay'])
            ->make(true);
        }
        return view('admin.user.index', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('Admin\UserManagementController@index'), 'name'=>"Users List"], ['name'=>"Add User"]
        ];
        $businesses = Business::get();
        return view('admin.user.create', compact('breadcrumbs', 'businesses'));
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
            'business_id' => ['required'],
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
                'redirect' => action('Admin\UserManagementController@index')
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('Admin\UserManagementController@index'), 'name'=>"Users List"], ['name'=>"Edit User"]
        ];
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user','breadcrumbs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
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

            if ($request->hasFile('picture')) {
                $image = $request->file('picture');
                $image_name = sha1(time()) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/profile/profile-picture/') , $image_name);
                $data['picture'] = $image_name;
            }
            if($request->password != null){
              $data['password'] = Hash::make($request->password);
            }
            if($user->role != "Owner") {
                $permissions = User::ownerPermissions();
                $user->revokePermissionTo($permissions);
                 if($request->has('permissions')){
                    $permissions = Permission::whereIn('name', $request->permissions)->get();
                    $user->givePermissionTo($permissions);
                }
            }
            $user = $user->update($data);

            $output = ['success' => 1,
                'msg' => 'User updated successfully!',
                'redirect' => action('Admin\UserManagementController@index')
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $user = User::findOrFail($id);
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
        $action = action('Admin\UserManagementController@destroy', $user->id);
        $title = 'user ' . $user->fullName();
        return view('layouts.delete', compact('action' , 'title'));
    }

    public function settings(Request $request){
      return view('user.settings');
    }

    public function Shops(User $user)
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('Admin\ShopManagementController@index'), 'name'=>"Shop List"], ['name'=>"Shops"]
        ];
        if ( request()->ajax()) {
           $shop = Shop::where('business_id', $user->business->id)->orderBy('shop.updated_at', 'desc');
            return Datatables::eloquent($shop)
            ->editColumn('site', function(Shop $shop) {
                            return '<img src="'.asset('images/shop/icon/'.$shop->site.'.png').'" style="display:block; width:100%; height:auto;">';
                        })
            ->addColumn('warehouse_name', function(Shop $shop) {
                return isset($shop->warehouse->name)?$shop->warehouse->name:'[Deleted Warehouse]';
            })
            ->addColumn('statusChip', function(Shop $shop) {
                            $html = '';
                            if($shop->active == 1){
                                $html = '<div class="chip chip-primary"><div class="chip-body"><div class="chip-text">Active</div></div></div>';
                            }else if($shop->active == 2){
                                $html = '<div class="chip chip-info"><div class="chip-body"><div class="chip-text">Syncing</div></div></div>';
                            }
                           return $html;
                        })
            ->addColumn('orders', function(Shop $shop) {
                            $html = $shop->orders()->whereDate('created_at','=',date('Y-m-d'))->count();
                            $html = '<div class="chip chip-info"><div class="chip-body"><div class="chip-text">'.$html.'</div></div></div>';
                           return $html;
                        })
            ->addColumn('pending_count', function(Shop $shop) {
                           return '<div class="chip chip-danger"><div class="chip-body"><div class="chip-text">'.
                        $shop->orders('pending')->count().'</div></div></div>';
                        })
            ->addColumn('ready_to_ship_count', function(Shop $shop) {
                           return '<div class="chip chip-warning"><div class="chip-body"><div class="chip-text">'.
                        $shop->orders('ready_to_ship')->count().'</div></div></div>';
                        })
            ->addColumn('shipped_count', function(Shop $shop) {
                           return '<div class="chip chip-success"><div class="chip-body"><div class="chip-text">'.
                        $shop->orders('shipped')->count().'</div></div></div>';
                        })
            ->addColumn('delivered_count', function(Shop $shop) {
                           return '<div class="chip chip-success"><div class="chip-body"><div class="chip-text">'.
                        $shop->orders('delivered')->count().'</div></div></div>';
                        })
            ->addColumn('products', function(Shop $shop) {
                           $product_count =  Products::where('shop_id','=',$shop->id)->get()->count();
                           return '<div class="chip chip-info"><div class="chip-body"><div class="chip-text">'.$product_count.'</div></div></div>';
                        })
            ->addColumn('action', function(Shop $shop) {
                    $actions = '<div class="btn-group dropup mr-1 mb-1">
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                    Action<span class="sr-only">Toggle Dropdown</span></button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item modal_button" href="#" data-href="'. action('Admin\ShopManagementController@edit', $shop->id) .'"><i class="fa fa-edit aria-hidden="true""></i> Edit</a>
                    </div></div>';
                    return $actions;
             })
            ->rawColumns(['site', 'shipped_count', 'pending_count', 'ready_to_ship_count', 'delivered_count', 'statusChip','orders','products', 'action'])
            ->make(true);
        }
        return view('admin.user.shop', [
            'user' => $user,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
