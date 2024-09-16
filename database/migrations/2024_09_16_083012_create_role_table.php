<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {


            $role = Role::create(['name' => 'administrator']);

            $permission_create = Permission::create(['name' => 'create plats']);
            $permission_edit = Permission::create(['name' => 'edit plats']);
            $permission_destroy = Permission::create(['name' => 'destroy plats']);

            $role->givePermissionTo($permission_create);
            $role->givePermissionTo($permission_edit);
            $role->givePermissionTo($permission_destroy);





    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role');
    }
};
