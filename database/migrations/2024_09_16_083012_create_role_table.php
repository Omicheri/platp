<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $admin = Role::create(['name' => 'administrator']);
        $user = Role::create(['name' => 'user']);

        $permission_create = Permission::create(['name' => 'create plats']);
        $permission_edit = Permission::create(['name' => 'edit plats']);
        $permission_destroy = Permission::create(['name' => 'destroy plats']);
        $permission_view = Permission::create(['name' => 'view plats']);

        $admin->givePermissionTo($permission_create);
        $admin->givePermissionTo($permission_edit);
        $admin->givePermissionTo($permission_destroy);
        $admin->givePermissionTo($permission_view);
        $user->givePermissionTo($permission_view);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role');
    }
};
