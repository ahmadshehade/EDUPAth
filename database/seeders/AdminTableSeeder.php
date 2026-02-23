<?php

namespace Database\Seeders;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->delete();
        User::firstOrCreate([
            'name'=>[
                'en'=>'Admin',
                'ar'=>'الادمن',
            ],
            'email'=>"admin@admin",
            "password"=>Hash::make('P@ssw0rd'),

        ]);

        $user=User::findOrFail(1);
        $user->assignRole(UserRoles::Admin->value);

    }
}
