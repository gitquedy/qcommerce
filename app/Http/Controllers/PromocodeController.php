<?php

namespace App\Http\Controllers;

use Validator;
use App\Promocode;
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
            ['link'=>"/",'name'=>"Admin"],['link'=> action('PromocodeController@index'), 'name'=>"Proomocode"], ['name'=>"Promocode List"]
        ];
        if ( request()->ajax()) {
            $promocode = Promocode::orderBy('updated_at', 'desc');
            return Datatables($promocode)
            ->editColumn('code', function(Promocode $promocode) {
                return '<h3><span class="badge badge-primary">'.$promocode->code.'</span></h3>';

            })
            ->editColumn('starts_at', function(Promocode $promocode) {
                return date("F d, Y", strtotime($promocode->starts_at));

            })
            ->editColumn('expires_at', function(Promocode $promocode) {
                return date("F d, Y", strtotime($promocode->expires_at));

            })
            ->addColumn('action', function(Promocode $promocode) {
                    $edit = '<a class="disabled dropdown-item" href="'. action('PromocodeController@edit', $promocode->id) .'"><i class="fa fa-edit" aria-hidden="true"></i> Edit</a>';

                    $delete = '<a class="disabled dropdown-item modal_button " href="#" data-href="'. action('PromocodeController@delete', $promocode->id).'" ><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';

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
            'max_uses_business' => 'required|integer|min:1',
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
            $customer = Promocode::create($data);

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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Promocode  $promocode
     * @return \Illuminate\Http\Response
     */
    public function destroy(Promocode $promocode)
    {
        //
    }


    public function delete(Promocode $promocode, Request $request){
      if($promocode->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this promocode');
      }
        $action = action('CustomerController@destroy', $promocode->id);
        $title = 'promocode ' . $promocode->fullName();
        return view('layouts.delete', compact('action' , 'title'));
    }
}
