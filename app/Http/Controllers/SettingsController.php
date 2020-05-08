<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use App\Settings;
use App\OrderRef;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('SalesController@index'), 'name'=>"Settings"], ['name'=>"General Settings"]
        ];
       
        $Settings = Settings::where('business_id', Auth::user()->business_id)->first();
        return view('settings.index', [
            'breadcrumbs' => $breadcrumbs,
            'settings' => $Settings,
        ]);
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
        $validator = Validator::make($request->all(),[
            'sales_prefix' => 'required|string|max:255',
            'quote_prefix' => 'required|string|max:255',
            'purchase_prefix' => 'required|string|max:255',
            'transfer_prefix' => 'required|string|max:255',
            'delivery_prefix' => 'required|string|max:255',
            'payment_prefix' => 'required|string|max:255',
            'return_prefix' => 'required|string|max:255',
            'adjustment_prefix' => 'required|string|max:255',
        ]);
        

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $data = $request->except(['_token', 'store_type']);

            $q = Settings::where('business_id', $user->business_id)->first();
            foreach ($data as $col => $val) {
                $q->$col = $val;
            }
            $q->save();

            $output = ['success' => 1,
                'msg' => 'Settings updated successfully!',
                'redirect' => action('SettingsController@index')
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
     * @param  \App\Settings  $Settings
     * @return \Illuminate\Http\Response
     */
    public function show(Settings $Settings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Settings  $Settings
     * @return \Illuminate\Http\Response
     */
    public function edit(Settings $Settings)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Settings  $Settings
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Settings $Settings)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Settings  $Settings
     * @return \Illuminate\Http\Response
     */
    public function destroy(Settings $Settings)
    {
        //
    }

}
