<?php

namespace Database\Seeders;

use App\Enums\UserRoles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles=UserRoles::cases();
        foreach($roles as $role){
          Role::create([
            'name'=>$role->value,
            'guard_name'=>'api',
          ]);
        }
    }
}
