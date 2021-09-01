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
use App\ShopPermission;
use Auth;
use App\Notifications\MailResetPassword;

class User extends Authenticatable implements MustVerifyEmail
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

    // public function permissions(){
    //     return $this->hasMany(Permission::class, 'model_id', 'id');
    // }

    public function shopPermissions(){
        return $this->hasMany(ShopPermission::class, 'user_id', 'id');
    }

    public function warehousePermissions(){
        return $this->hasMany(WarehousePermission::class, 'user_id', 'id');
    }

    public function checkAllowedSite($site){
        if($this->business->shops->contains('site', $site)){
            return true;
        }else{
            return false;
        }
        
    }

    public function checkFirstAllowedSite(){
        $sites = ['lazada', 'shopee', 'shopify', 'woocommerce'];
        foreach($sites as $site){
            if($this->checkAllowedSite($site)){
                return $site;
            }
        }
        return 'lazada';
    }


    public static function giveOwnerPermissionToOwners(){
        $users = User::where('role', 'Owner')->get();

        foreach($users as $user){
            $user->giveOwnerPermissions();
        }
    }

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
        else if ($this->status == 0) {
            $status ='<div class="chip chip-danger"><div class="chip-body"><div class="chip-text">Suspended</div></div></div>';
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
            $data[$date] = number_format(Order::whereIn('shop_id', $shop_ids)->whereNotIn('status', Order::statusNotIncludedInSales())->whereBetween('created_at', [$date, $dates[$iteration]])->sum('price'), 2);
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
            $data[$date] =  number_format(Order::whereIn('shop_id', $shop_ids)->whereBetween('created_at', [$date, $dates[$iteration]])->count());
            $iteration++;
        }
        return $data;
    }

    public function currentPendingOrders($shop_ids){
        $data = 0;
        $data = number_format(Order::whereIn('shop_id', $shop_ids)->whereIn('status', ['pending'])->count());
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
            $data[$date] =  number_format(Order::whereIn('shop_id', $shop_ids)->whereNotIn('status', Order::statusNotIncludedInSales())->whereBetween('created_at', [$date, $dates[$iteration]])->sum('price'), 2);
            $iteration++;
        }
        return $data;
    }

    public function fullName(){
        return $this->first_name . ' ' . $this->last_name;
    }

    public function imageUrl(){
        return asset('images/profile/profile-picture/'. $this->picture);
    }

    public function getNameAndImgDisplay(){
         return '<div class="text-primary font-medium-2 text-bold-600">'. $this->fullName() .' </div>' . '<img src="'. $this->imageUrl() .'" style="width:60px; height:60px">';
    }

    public function updateUserStatus() {
        $user = $this;
        if ($user->business->subscription() !== null) {
            if ($user->business->subscription()->plan_id == 5 || $user->business->users()->count() <= $user->business->subscription()->plan->users) {
                $user->business->users()->update(['status' => 1]);
            }
            else {
                $active_count = $user->business->subscription()->plan->users;
                $suspended_count = $user->business->users()->count() - $active_count;

                $active_users = $user->business->users()->orderBy('created_at', 'asc')->take($active_count)->get();
                foreach ($active_users as $active) {
                    $active->status = 1;
                    $active->save();
                }

                $suspended_users = $user->business->users()->orderBy('created_at', 'desc')->take($suspended_count)->get();
                foreach ($suspended_users as $suspend) {
                    $suspend->status = 0;
                    $suspend->save();
                }
            }
        }
        else {
            $user->business->users()->where('role', '!=', 'Owner')->update(['status' => 0]);
        }
    }

    public function sendPasswordResetNotification($token) {
        $this->notify(new MailResetPassword($token));
    }
}
