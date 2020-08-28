<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use App\Settings;
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
       
        $s = Settings::where('business_id', Auth::user()->business_id)->first();
        return view('settings.index', [
            'breadcrumbs' => $breadcrumbs,
            'settings' => $s,
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Settings  $setting
     * @return \Illuminate\Http\Response
     */
    public function show(Settings $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Settings  $setting
     * @return \Illuminate\Http\Response
     */
    public function edit(Settings $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Settings  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Settings $setting)
    {
        $validator = Validator::make($request->all(),[
            'sales_prefix' => 'required|string|max:191',
            'quote_prefix' => 'required|string|max:191',
            'purchase_prefix' => 'required|string|max:191',
            'transfer_prefix' => 'required|string|max:191',
            'delivery_prefix' => 'required|string|max:191',
            'payment_prefix' => 'required|string|max:191',
            'return_prefix' => 'required|string|max:191',
            'adjustment_prefix' => 'required|string|max:191',
            'customer_name_format' => 'required|string|max:191',
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $data = $request->except(['_token', '_method']);

            foreach ($data as $col => $val) {
                $setting->$col = $val;
            }
            $setting->save();
 
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
     * Remove the specified resource from storage.
     *
     * @param  \App\Settings  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Settings $setting)
    {
        //
    }
}
