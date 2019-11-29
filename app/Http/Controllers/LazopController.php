<?php

namespace App\Http\Controllers;

use App\Lazop;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LazopController extends Controller
{

    public function receive(){
        $code = $request->get('code');
        $client = new LazopClient("https://auth.lazada.com/rest", Api::get_api_key(), Api::get_api_secret());
        $r = new LazopRequest("/auth/token/create");
        $r->addApiParam("code", $code);
        $response = $client->execute($r);

        $data = json_decode($response, true);
        
        if(! array_key_exists('account', $data)){
            if($request->user()){
                return redirect('/')->with(['status', $data['message'], 'alert-class', 'danger']);
            }else{
                return redirect('login');
            }
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Lazop  $lazop
     * @return \Illuminate\Http\Response
     */
    public function show(Lazop $lazop)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Lazop  $lazop
     * @return \Illuminate\Http\Response
     */
    public function edit(Lazop $lazop)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Lazop  $lazop
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lazop $lazop)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Lazop  $lazop
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lazop $lazop)
    {
        //
    }
}
