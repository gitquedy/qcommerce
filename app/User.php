<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Utilities;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'picture', 'phone', 'email', 'password', 'role',
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

    public function formatName() {
        return $this->last_name.", ".$this->first_name;
    }

    public function shops(){
        return $this->hasMany(Shop::class, 'user_id', 'id');
    }

    public function isAdmin(){
        if (env('ADMIN') == $this->email){
            return true;
        }else{
            return false;
        }
    }

    public function updateToken()
    {
        $token = Str::random(80);

        $this->forceFill([
            'api_token' => hash('sha256', $token),
        ])->save();

        return $token;
    }

    public function totalSalesToday($shops){
        $shops = $shops->pluck('id');
        $data = [];
        $dates = Utilities::getToday24Hours();
        $iteration = 1;
        foreach($dates as $key => $date){
            if($iteration == 25){ //skip last
                continue;
            }
            $data[$date] =  Order::whereIn('shop_id', $shops)->whereBetween('created_at', [$date, $dates[$iteration]])->sum('price');
            $iteration++;
        }

        return (object) $data;
    }

    public function totalOrdersToday($shops){
        $shops = $shops->pluck('id');
        $data = [];
        $dates = Utilities::getToday24Hours();
        $iteration = 1;
        foreach($dates as $key => $date){
            if($iteration == 25){ //skip last
                continue;
            }
            $data[$date] =  Order::whereIn('shop_id', $shops)->whereBetween('created_at', [$date, $dates[$iteration]])->count();
            $iteration++;
        }
        return (object) $data;
    }

    public function currentPendingOrders($shops){
        $shops = $shops->pluck('id');
        $data = 0;
        $data = Order::whereIn('shop_id', $shops)->whereDate('created_at', Carbon::today())->whereIn('status', ['ready_to_ship', 'pending', 'UNPAID', 'READY_TO_SHIP'])->count();
        return $data;
    }

    public function totalMonthlySales($shops){
        $shops = $shops->pluck('id');
        $dates =Utilities::getMonthsDates(7);
        $iteration = 1;
        $data = [];
        foreach($dates as $key => $date){
            if($iteration == 8){ //skip last
                continue;
            }
            $data[$date] =  Order::whereIn('shop_id', $shops)->whereNotIn('status', Order::statusNotIncludedInSales())->whereBetween('created_at', [$date, $dates[$iteration]])->sum('price');
            $iteration++;
        }
        return $data;
    }
}
