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
        $user=User::create([
            'email' => 'john@doe.com',
            'username'=>'baki123',
            'password' => Hash::make('password'),
            'phone'=>'1'
        ]);
        $admin=User::create([
                'email' => 'admin@admin.com',
                'username' => 'admin123',
                'password' => Hash::make('password'),
                'phone'=>'2'
            ]);
        $user->card()->create([
            'number'=>'515616651',
            'cvv'=>'61',
            'amount'=>65651
        ]);
        $admin->card()->create([
            'number'=>'56841516651',
            'cvv'=>'610',
            'amount'=>655651
        ]);
        $admin->assignRole('admin');

    }
}
