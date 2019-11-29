<?php

namespace App\Http\Controllers;

use App\Crud;
use App\Utilities;
use Illuminate\Http\Request;
use Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class CrudController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumbs = [
            ['link'=> "/", 'name'=> "Home"],['link' => action('CrudController@index'), 'name' => "Crud"], ['name' => "Crud Sample"]
                        ];
            return view('crud.index', ['breadcrumbs' => $breadcrumbs]);
    }

    public function listView(Request $request){
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('CrudController@listView'), 'name'=>"Crud List"],['link'=> action('CrudController@listView', ['status' => 'Archived']), 'name'=>"Archived List"], ['name'=>"Crud List"]
        ];
        if ( request()->ajax()) {
           $crud = Crud::select('*');
           if($request->get('status')){
                $crud->where('status', $request->get('status'));
           }

           $crud->orderBy('updated_at', 'desc');
            return Datatables::eloquent($crud)
            ->addColumn('checkbox', function(Crud $crud) {
                            return '<input type="checkbox" id='. $crud->id .'" name="checkbox" />';
                        })
            ->addColumn('statusWithColor', function(Crud $crud) {
                            return '<div class="chip chip-'. $crud->getStatusColor() .'"><div class="chip-body"><div class="chip-text">'. $crud->status  .'</div></div></div>';
                        })
            ->addColumn('action', function(Crud $crud) {
                            $html = Utilities::editButton(action('CrudController@edit', [$crud->id]));
                            $html .= Utilities::deleteButton(action('CrudController@delete', [$crud->id]));
                            return $html;
                        })
            ->rawColumns(['action', 'checkbox', 'statusWithColor'])
            ->make(true);
        }

        return view('/crud/listView', [
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
            ['link'=>"/",'name'=>"Home"],['link'=> action('CrudController@listView'), 'name'=>"Crud List"],['name'=>"Add New Crud"]
        ];
      return view('crud.create', [
          'breadcrumbs' => $breadcrumbs
      ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), ['name' => ['required']]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
            }
        try {
            DB::beginTransaction();
            Crud::create($request->all());
            DB::commit();
            $output = ['success' => 1,
                        'msg' => 'Crud added successfully!',
                        'redirect' => action('CrudController@listView')
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Crud  $crud
     * @return \Illuminate\Http\Response
     */
    public function show(Crud $crud)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Crud  $crud
     * @return \Illuminate\Http\Response
     */
    public function edit(Crud $crud)
    {
        return view('crud.edit', compact('crud'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Crud  $crud
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Crud $crud)
    {
        $validator = Validator::make($request->all(), ['name' => ['required', 'unique:crud,name,' . $crud->id]]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
            }
        try {
            DB::beginTransaction();
            $data = $request->all();
            $crud->touch();
            $crud = $crud->update($data);
            DB::commit();
            $output = ['success' => 1,
                        'msg' => 'Crud updated successfully!'
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Crud  $crud
     * @return \Illuminate\Http\Response
     */
    public function destroy(Crud $crud)
    {
        try {
            DB::beginTransaction();
            $crud->delete();
            DB::commit();
            $output = ['success' => 1,
                        'msg' => 'Crud successfully deleted!'
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

    public function delete(Crud $crud){
        $action = action('CrudController@destroy', $crud->id);
        $title = 'crud ' . $crud->name;
        return view('layouts.delete', compact('action' , 'title'));
    }

    public function massDelete(Request $request){
        try {
            $ids = $request->get('ids');
            DB::beginTransaction();
            Crud::whereIn('id', $ids)->delete(); 
            DB::commit();
            $output = ['success' => 1,
                        'msg' => 'Cruds successfully deleted!',
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
    public function massArchived(Request $request){
        try {
            $ids = $request->get('ids');
            DB::beginTransaction();
            Crud::whereIn('id', $ids)->update(['status' => 'Archived']); 
            DB::commit();
            $output = ['success' => 1,
                        'msg' => 'Cruds successfully deleted!',
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
    
}
