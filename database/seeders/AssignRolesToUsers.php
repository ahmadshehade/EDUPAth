<?php

namespace Database\Seeders;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use function PHPUnit\Framework\isEmpty;

class AssignRolesToUsers extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::with('roles')->get();
        $usersWithoutRoles = $users->filter(fn($user) => $user->roles->isEmpty());
        $roles=[UserRoles::Instructor->value,UserRoles::Student->value];
        foreach($usersWithoutRoles as $user){
            $randRole=$roles[array_rand($roles)];
            $user->assignRole($randRole);
        }
        
    }
}
