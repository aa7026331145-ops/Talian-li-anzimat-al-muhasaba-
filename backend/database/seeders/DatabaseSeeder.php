<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'sanctum']);
        $admin      = Role::firstOrCreate(['name' => 'admin',       'guard_name' => 'sanctum']);
        Role::firstOrCreate(['name' => 'accountant', 'guard_name' => 'sanctum']);

        // Create a demo tenant
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'demo'],
            [
                'name'      => 'شركة تاليان التجريبية',
                'email'     => 'demo@talian-erp.local',
                'currency'  => 'SAR',
                'locale'    => 'ar',
                'is_active' => true,
            ]
        );

        // Create super-admin user (no tenant)
        $superUser = User::firstOrCreate(
            ['email' => 'superadmin@talian-erp.local'],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make('SuperAdmin@2024!'),
                'is_active' => true,
            ]
        );
        $superUser->syncRoles([$superAdmin]);

        // Create tenant admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@demo.talian-erp.local'],
            [
                'name'      => 'مدير النظام',
                'tenant_id' => $tenant->id,
                'password'  => Hash::make('Admin@2024!'),
                'is_active' => true,
            ]
        );
        $adminUser->syncRoles([$admin]);
    }
}
