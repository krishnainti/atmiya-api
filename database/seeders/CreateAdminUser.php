<?php
namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class CreateAdminUser extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'admin']);
        $adminPermission = Permission::create(['name' => 'admin']);
        $adminRole->givePermissionTo($adminPermission);

        $userRole = Role::create(['name' => 'standard_user']);
        $userPermission = Permission::create(['name' => 'standard_user']);
        $userRole->givePermissionTo($userPermission);

        $adminUser = User::where("email", env("ADMIN_EMAIL"))->first();

        if (!$adminUser) {
            $adminUser = User::create([
                'name' => 'Admin',
                'email' => env("ADMIN_EMAIL"),
                'password' => env("ADMIN_PASSWORD")
            ]);

            $adminUser->assignRole('admin');
        }

        echo "Done Admin Creation";



    }
}
