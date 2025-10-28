<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
           
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(
                ['permission_key' => $perm['permission_key']],
                ['description' => $perm['description']]
            );
        }
    }
}
