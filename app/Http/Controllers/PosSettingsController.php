<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use App\PosSettings;
use App\OrderRef;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('SalesController@index'), 'name'=>"POS Settings"], ['name'=>"Order Reference"]
        ];
       
        $PosSettings = PosSettings::where('business_id', Auth::user()->business_id)->first();
        return view('possettings.index', [
            'breadcrumbs' => $breadcrumbs,
            'order_ref' => $PosSettings,
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
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $pos = PosSettings::where('business_id', $user->business_id)->first();
            $data = $request->except(['_token']);
            foreach ($data as $col => $val) {
                $pos->$col = $val;
            }
            $pos->save();

            $output = ['success' => 1,
                'msg' => 'POS Settings updated successfully!',
                'redirect' => action('PosSettingsController@index')
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
     * @param  \App\PosSettings  $posSettings
     * @return \Illuminate\Http\Response
     */
    public function show(PosSettings $posSettings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PosSettings  $posSettings
     * @return \Illuminate\Http\Response
     */
    public function edit(PosSettings $posSettings)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PosSettings  $posSettings
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PosSettings $posSettings)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PosSettings  $posSettings
     * @return \Illuminate\Http\Response
     */
    public function destroy(PosSettings $posSettings)
    {
        //
    }
}
