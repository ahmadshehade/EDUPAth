<?php

namespace Database\Seeders;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
       $user=  User::create([
            'name' => [
                'ar'=>'الطالب',
                'en'=>'student'
            ],
            'email' => 'eng.ahmad.shehade@gmail.com',
            'password' => Hash::make('P@ssw0rd123'),
        ]);

        $user->assignRole(UserRoles::Student->value);
    }
}
