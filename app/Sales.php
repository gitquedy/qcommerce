<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class Sales extends Model
{
    protected $table = 'sales';

	protected $fillable = [
        'business_id', 'customer_id', 'warehouse_id', 'customer_first_name', 'customer_last_name', 'date', 'reference_no', 'note', 'status', 'total', 'discount', 'grand_total', 'paid', 'payment_status', 'created_by', 'updated_by'
    ];

    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
	}

    public function savedCustomer(){
        $format = $this->business->settings->customer_name_format;
        switch ($format) {
            case "Lname, Fname":
                return $this->customer_last_name.", ".$this->customer_first_name;
              break;
            case "Fname Lname":
            default:
                return $this->customer_first_name." ".$this->customer_last_name;
              break;
        }
    }

    public function business(){
        return $this->belongsTo(Business::class, 'business_id', 'id');
    }

    public function payments(){
        return $this->hasMany(Payment::class, 'sales_id', 'id');
    }

    public function warehouse() {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }

	public function items(){
		return $this->hasMany('App\SaleItems', 'sales_id');
	}

    public static function get_dashboard_sales($status="",$type="",$current_user=true){
        $query = DB::table('sales'); 
        if($current_user){
            $query->where('business_id', Auth::user()->business_id);
        }
        if($status!=""){
           $query->where('status','=',$status); 
        }
        // else {
        //     $query->where('status', '!=', 'canceled');
        // }
        
        if($type=='month'){
            $start = date('Y-m-01');
            
            $date=date_create($start);
            
            date_modify($date,"+1 month");
            
            $end = date_format($date,"Y-m-d");
            
              $query->where('date', '>=', $start);
              $query->where('date', '<=', $end);
            
        }
        
        if($type=='today'){
            $query->whereDate('date',"=",date('Y-m-d'));
        }
        
        if($type=="two_month"){
            
            $start = date('Y-m-01');
            
            $date=date_create($start);
            
            date_modify($date,"-1 month");
            
            $pre = date_format($date,"Y-m-d");
            
            $date=date_create($start);
            
            date_modify($date,"+1 month");
            
            $end = date_format($date,"Y-m-d");
            
            $query->where('date', '>=', $pre);
            $query->where('date', '<=', $end);
            
        }
        
        if($type=="last_6_month"){
            
           // $end = date('Y-m-d');
            
            $date=date_create(date('Y-m-d'));
            $end = date_modify($date,"+1 days");
            
            $date=date_create(date('Y-m-01'));
            
            date_modify($date,"-6 month");
            
            $start = date_format($date,"Y-m-d");
            
            
            $query->where('date', '>=', $start);
            $query->where('date', '<=', $end);
            
        }
        return $query->get();
    }

    public static function get_dashboard_performance($type="") {
        $query = DB::table('sales');
        $query->where('business_id', Auth::user()->business_id);
        $query->where('status', '!=', 'canceled');

        if($type=='today'){
            $query->whereDate('date',"=",date('Y-m-d'));
        }
        if($type=='yesterday'){
            $query->whereDate('date',"=", date('Y-m-d', strtotime("-1 day")));
        }

        if($type=='week'){
            $date = date('Y-m-d');
            $ts = strtotime($date);
            $start_t = (date('w', $ts) == 0) ? $ts : strtotime('last sunday', $ts);
            $end_t = strtotime('next saturday', $start_t);
            $start = date("Y-m-d", $start_t);
            $end = date("Y-m-d", $end_t);
            $query->where('date', '>=', $start);
            $query->where('date', '<=', $end);
        }

        if($type=='month'){
            $start = date('Y-m-01');
            
            $date=date_create($start);
            
            date_modify($date,"+1 month");
            
            $end = date_format($date,"Y-m-d");
            
              $query->where('date', '>=', $start);
              $query->where('date', '<=', $end);
            
        }

        $result = $query->get();
        $total = 0;
        
        foreach($result as $r) {
            $total += self::tofloat($r->grand_total);
        }


        return number_format($total, 2);
    }


    public static function tofloat($num) {
        $dotPos = strrpos($num, '.');
        $commaPos = strrpos($num, ',');
        $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
            ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);
      
        if (!$sep) {
            return floatval(preg_replace("/[^0-9]/", "", $num));
        }

        return floatval(
            preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
            preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
        );
    }
}