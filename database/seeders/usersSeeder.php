<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\User;
class usersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'email' => 'john@doe.com',
            'username'=>'baki123',
            'password' => Hash::make('password')
        ]);
        $admin=User::create([
                'email' => 'admin@admin.com',
                'username' => 'admin123',
                'password' => Hash::make('password')
            ]);
        $admin->assignRole('admin');

    }
}
