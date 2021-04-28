<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Shop;
use App\Products;
use App\Sku;
use Helper;
use Auth;
use DB;
use App\Library\lazada\lazop\LazopRequest;
use App\Library\lazada\lazop\LazopClient;
use App\Library\lazada\lazop\UrlConstants;
use App\Lazop;
use App\Utilities;
use Carbon\Carbon;




class ExpController extends Controller
{
    
    public function exp1(){
        
        // $structure = array('tables'=>array(
        // 	'new_table'=>array('id'=>'primary','name'=>"",'mobile'=>"")
        // ));
        
        
        
        // $records = array();
        
        // for ($i=0; $i <500 ; $i++) { 
        // $records['new_table'][] = array('id'=>$i,'name'=>"jerico",'mobile'=>"21555");
        // }
        
        // $data = array('structure' =>$structure,'records' =>$records);
        
        // $content = base64_encode(json_encode($data));
        
        // $myfile = fopen("dbx.txt", "w") or die("Unable to open file!");
        // fwrite($myfile, $content);
        // fclose($myfile);
        
        
        // $getData = file("dbx.txt");
        
        // if (isset($getData[0])) {
        // 	$resp = base64_decode($getData[0]);
        // 	$jsOBJ = json_decode($resp);
        // 	//echo $resp;
        // 	//print_r($jsOBJ);
        // 	echo "set";
        // }else{
        // 	echo "Invalid Response";
        // }
        
        
        return view('exp.index', [
            'breadcrumbs' => array(),
            'all_shops' => array(),
            'statuses' => array(),
        ]);
    }
    
}

