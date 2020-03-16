<?php

namespace App;

// use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Utilities;
use Spatie\Permission\Models\Permission;

class User extends Authenticatable
{
    // use HasApiTokens, Notifiable;
use Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'picture', 'phone', 'email', 'password', 'role', 'business_id', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function ownerPermissions(){
        $permissions = Permission::all();
        return $permissions;
    }

    public function giveOwnerPermissions(){
        $permissions = $this->ownerPermissions();
        $this->givePermissionTo($permissions);
    }

    public function formatName() {
        return $this->last_name.", ".$this->first_name;
    }

    public function business(){
        return $this->belongsTo(Business::class, 'business_id', 'id');
    }

    public function isAdmin(){
        if (env('ADMIN') == $this->email){
            return true;
        }else{
            return false;
        }
    }

    public function getStatusDisplay(){
        $status = '';
        if($this->status == 1){
            $status ='<div class="chip chip-success"><div class="chip-body"><div class="chip-text">Active</div></div></div>';
        }
        return $status;
    }

    public function updateToken()
    {
        $token = Str::random(80);

        $this->forceFill([
            'api_token' => hash('sha256', $token),
        ])->save();

        return $token;
    }

    public function totalSalesToday($shop_ids){
        $data = [];
        $dates = Utilities::getToday24Hours();
        $iteration = 1;
        foreach($dates as $key => $date){
            if($iteration == 25){ //skip last
                continue;
            }
            $data[$date] =  Order::whereIn('shop_id', $shop_ids)->whereBetween('created_at', [$date, $dates[$iteration]])->sum('price');
            $iteration++;
        }

        return $data;
    }

    public function totalOrdersToday($shop_ids){
        $data = [];
        $dates = Utilities::getToday24Hours();
        $iteration = 1;
        foreach($dates as $key => $date){
            if($iteration == 25){ //skip last
                continue;
            }
            $data[$date] =  Order::whereIn('shop_id', $shop_ids)->whereBetween('created_at', [$date, $dates[$iteration]])->count();
            $iteration++;
        }
        return $data;
    }

    public function currentPendingOrders($shop_ids){
        $data = 0;
        $data = Order::whereIn('shop_id', $shop_ids)->whereDate('created_at', Carbon::today())->whereIn('status', ['ready_to_ship', 'pending', 'UNPAID', 'READY_TO_SHIP'])->count();
        return $data;
    }

    public function totalMonthlySales($shop_ids){
        $dates = Utilities::getMonthsDates(7);
        $iteration = 1;
        $data = [];
        foreach($dates as $key => $date){
            if($iteration == 8){ //skip last
                continue;
            }
            $data[$date] =  Order::whereIn('shop_id', $shop_ids)->whereNotIn('status', Order::statusNotIncludedInSales())->whereBetween('created_at', [$date, $dates[$iteration]])->sum('price');
            $iteration++;
        }
        return $data;
    }

    public function fullName(){
        return $this->first_name . ' ' . $this->last_name;
    }

    public function imageUrl(){
        return asset('images/profile/profile-picture/'.$this->picture);
    }

    public function getNameAndImgDisplay(){
         return '<div class="text-primary font-medium-2 text-bold-600">'. $this->fullName() .' </div>' . '<img src="'. $this->imageUrl() .'" style="width:60px; height:60px">';
    }
}
