<?php

namespace App\Http\Controllers;

use Validator;
use App\Promocode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromocodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Admin"],['link'=> action('PromocodeController@index'), 'name'=>"Promocode"], ['name'=>"Promocode List"]
        ];
        if ( request()->ajax()) {
            $promocode = Promocode::orderBy('updated_at', 'desc');
            return Datatables($promocode)
            ->editColumn('code', function(Promocode $promocode) {
                return '<h3><span class="badge badge-primary">'.$promocode->code.'</span></h3>';

            })
            ->editColumn('discount_range', function(Promocode $promocode) {
                if($promocode->discount_range == "first") {
                    return "First Payment";
                }
                else if($promocode->discount_range == "all") {
                    return "All Payments";
                }
                else {
                    return "Unknown";
                }
            })
            ->editColumn('starts_at', function(Promocode $promocode) {
                return date("F d, Y", strtotime($promocode->starts_at));

            })
            ->editColumn('expires_at', function(Promocode $promocode) {
                return date("F d, Y", strtotime($promocode->expires_at));

            })
            ->addColumn('action', function(Promocode $promocode) {
                    $edit = '<a class="dropdown-item" href="'. action('PromocodeController@edit', $promocode->id) .'"><i class="fa fa-edit" aria-hidden="true"></i> Edit</a>';

                    $delete = '<a class="dropdown-item modal_button " href="#" data-href="'. action('PromocodeController@delete', $promocode->id).'" ><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';

                    $actions = '<div class="btn-group dropup mr-1 mb-1"><button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">Action<span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu">'.$edit.$delete.'</div></div>';
                    return $actions;
             })
            ->rawColumns(['action','code'])
            ->make(true); 
        }
        return view('promocode.index', [
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
            ['link'=>"/",'name'=>"Admin"],['link'=> action('PromocodeController@index'), 'name'=>"Promocode"], ['name'=>"Promocode  Create"]
        ];
        
        return view('promocode.create', ['breadcrumbs' => $breadcrumbs]);
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
            'code' => 'required|min:4|max:20|string',
            'name' => 'required|string|max:255',
            'max_uses' => 'required|integer|min:1',
            'discount_range' => 'required',
            'discount_amount' => 'required|integer|min:1',
            'discount_type' => 'required',
            'starts_at' => 'required|date|after_or_equal:today',
            'expires_at' => 'required|date|after_or_equal:starts_at',
            'description' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        try {
            $data = $request->all();
            DB::beginTransaction();
            $data['starts_at'] = date("Y-m-d H:i:s", strtotime($request->starts_at));
            $data['expires_at'] = date("Y-m-d H:i:s", strtotime($request->expires_at));
            $promocode = Promocode::create($data);

            $output = ['success' => 1,
                'msg' => 'Promocode added successfully!',
                'redirect' => action('PromocodeController@index')
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
     * @param  \App\Promocode  $promocode
     * @return \Illuminate\Http\Response
     */
    public function show(Promocode $promocode)
    {
       //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Promocode  $promocode
     * @return \Illuminate\Http\Response
     */
    public function edit(Promocode $promocode)
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Admin"],['link'=> action('PromocodeController@index'), 'name'=>"Promocode"], ['name'=>"Edit Promocode"]
        ];
        return view('promocode.edit', compact('promocode', 'breadcrumbs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Promocode  $promocode
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Promocode $promocode)
    {
         $validator = Validator::make($request->all(),[
            'code' => 'required|min:4|max:20|string',
            'name' => 'required|string|max:255',
            'max_uses' => 'required|integer|min:1',
            'discount_range' => 'required',
            'discount_amount' => 'required|integer|min:1',
            'discount_type' => 'required',
            'starts_at' => 'required|date|after_or_equal:today',
            'expires_at' => 'required|date|after_or_equal:starts_at',
            'description' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        try {
            $data = $request->all();
            DB::beginTransaction();
            $data['starts_at'] = date("Y-m-d H:i:s", strtotime($request->starts_at));
            $data['expires_at'] = date("Y-m-d H:i:s", strtotime($request->expires_at));
            $promocode = $promocode->update($data);

            $output = ['success' => 1,
                'msg' => 'Promocode updated successfully!',
                'redirect' => action('PromocodeController@index')
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
     * @param  \App\Promocode  $promocode
     * @return \Illuminate\Http\Response
     */
    public function destroy(Promocode $promocode)
    {
        try {
            DB::beginTransaction();
            $promocode->delete();
            DB::commit();
            $output = ['success' => 1,
                        'msg' => 'Promocode successfully deleted!'
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


    public function delete(Promocode $promocode, Request $request){
        $action = action('PromocodeController@destroy', $promocode->id);
        $title = 'promocode ' . $promocode->code;
        return view('layouts.delete', compact('action' , 'title'));
    }


    public function checkPromocode(Request $request) {
        $promocode = Promocode::where('code', $request->code)->where('starts_at', '<=', Carbon::now())->where('expires_at', '>=', Carbon::now())->whereRaw('uses < max_uses')->first();
        $output = ['success' => 0, 'msg' => ''];
        if($promocode) {
            $output['promocode'] = $promocode->id;
            $discount = '';
            $range = '';
            if($promocode->discount_type == "percentage") {
                $discount = $promocode->discount_amount."%";
            }
            else if($promocode->discount_type == "fixed") {
                $discount = $promocode->discount_amount.".00";
            }
            if($promocode->discount_range == "first") {
                $range = " on your first Payment!";
            }
            else if($promocode->discount_range == "all") {
                $range = " every Payment!";
            }
            $output['msg'] = "Promocode Valid. Discount: ".$discount." off ".$range;
            $output['success'] = 1;
        }
        else {
            $output['success'] = 0;
            $output['msg'] = "Invalid Promocode, Check the Promocode again.";
        }
        return response()->json($output);
    } 
}
