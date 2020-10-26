<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Business;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Settings;
use App\OrderRef;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $owner = Role::create(['name' => 'owner']);
        $staff = Role::create(['name' => 'staff']);

        $permissions = [
            ['name' => 'shop.manage'],
            ['name' => 'product.manage'],
            ['name' => 'order.manage'],
            ['name' => 'returnRecon.manage'],
            ['name' => 'payoutRecon.manage'],
            ['name' => 'shippingFeeRecon.manage'],
            ['name' => 'sku.manage'],
            ['name' => 'report.manage'],
            ['name' => 'user.manage'],
            ['name' => 'supplier.manage'],
            ['name' => 'category.manage'],
            ['name' => 'brand.manage'],
            ['name' => 'barcode.manage'],
            ['name' => 'sales.manage'],
            ['name' => 'warehouse.manage'],
            ['name' => 'adjustment.manage'],
            ['name' => 'transfer.manage'],
            ['name' => 'customer.manage'],
            ['name' => 'deposit.manage'],
            ['name' => 'payment.manage'],
            ['name' => 'settings.manage'],
            ['name' => 'purchase.manage'],
            ['name' => 'expense.manage'],
        ];
        foreach($permissions as $permission){
            Permission::create($permission);
        } 
        $owner_permissions = Permission::all();
        $staff_permissions = Permission::whereIn('id',[2,3,4,5,6])->get();
        

        Business::create([
            'name' => 'Quedy Technologies Admin',
            'location' => 'Pampanga'
        ]);

        Business::create([
            'name' => 'Quedy Technologies Test Seller',
            'location' => 'Pampanga'
        ]);


        $admin = User::create([
            'business_id' => 1,
        	'first_name' => 'Gabriel',
            'last_name' => 'Capili',
            'email' => 'dev.gabcapili@gmail.com',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
        ]);
        $owner = User::create([
            'business_id' => 2,
            'first_name' => 'Quedy',
            'last_name' => 'Dev',
            'email' => 'dev@quedy.com',
            'role' => 'Owner',
            'password' => Hash::make('admin123'),
        ]);

        $settings = Settings::create([
            'business_id' => 2,
        ]);
        $order_refs = OrderRef::create([
            'settings_id' => $settings->id,
        ]);

        $admin->givePermissionTo($owner_permissions);
        $owner->givePermissionTo($owner_permissions);
    }
}
