<?php

namespace Database\Seeders;

use App\Enums\UserRoles;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleTableSeeder extends Seeder
{
    public function run(): void
    {
        $roles = UserRoles::cases();
        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role->value, 'guard_name' => 'api']
            );
        }
    }
}