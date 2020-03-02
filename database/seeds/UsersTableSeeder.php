<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
        	'first_name' => 'Gabriel',
            'last_name' => 'Capili',
            'email' => 'dev.gabcapili@gmail.com',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
        ]);
        User::create([
            'first_name' => 'Quedy',
            'last_name' => 'Dev',
            'email' => 'dev.quedy@quedymedia.com',
            'role' => 'Seller',
            'password' => Hash::make('test123'),
        ]);
    }
}
