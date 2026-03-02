<?php

namespace Database\Seeders;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InstructorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $user=User::firstOrCreate([
            'email'=>'teacher@gmail.com',
            'name'=>[
                'en'=>'teacher',
                'ar'=>'المعلم',
            ],
            'password'=>Hash::make('P@ssw0rd123'),
        ]);

        $user->syncRoles(UserRoles::Instructor->value);
    }
}
