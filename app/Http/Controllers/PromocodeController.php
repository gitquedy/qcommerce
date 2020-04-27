<?php

namespace App\Http\Controllers;

use App\Promocode;
use Illuminate\Http\Request;

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
            ->addColumn('action', function(Promocode $promocode) {
                    $edit = '<a class="dropdown-item" href="'. action('PromocodeController@edit', $promocode->id) .'"><i class="fa fa-edit" aria-hidden="true"></i> Edit</a>';
                   
                    $view_payments = '<a class="dropdown-item toggle_view_modal" href="" data-action="'.action('PaymentController@viewPaymentModal', $promocode->id).'"><i class="fa fa-money" aria-hidden="true"></i> View Payments</a>';

                    $delete = '<a class="dropdown-item modal_button " href="#" data-href="'. action('PromocodeController@delete', $promocode->id).'" ><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';

                    $actions = '<div class="btn-group dropup mr-1 mb-1"><button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">Action<span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu">'.$edit.$delete.'</div></div>';
                    return $actions;
             })
            ->rawColumns(['action','status','payment_status'])
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
        //
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
}
