<?php

use App\Models\Permission;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPermissionForAccessAdminFunctions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $access_admin_function_permission = Permission::updateOrCreate([
            'code' => 'access_admin_functions',
            'name' => 'access admin functions'
        ]);

        $manage_users_permission = Permission::updateOrCreate([
            'code'      => 'manage_users',
            'name'      => 'manage users',
            'parent_id' => $access_admin_function_permission->id
        ]);

        Permission::updateOrCreate([
            'code'      => 'add_users',
            'name'      => 'add users',
            'parent_id' => $manage_users_permission->id
        ]);

        $edit_users_permission = Permission::updateOrCreate([
            'code'      => 'edit_users',
            'name'      => 'edit users',
            'parent_id' => $manage_users_permission->id
        ]);

        Permission::updateOrCreate([
            'code'      => 'edit_user_permissions',
            'name'      => 'edit user permissions',
            'parent_id' => $edit_users_permission->id
        ]);

        Permission::updateOrCreate([
            'code'      => 'delete_users',
            'name'      => 'delete users',
            'parent_id' => $manage_users_permission->id
        ]);

        $manage_chat_channels_permission = Permission::updateOrCreate([
            'code'      => 'manage_chat_channels',
            'name'      => 'manage chat channels',
            'parent_id' => $access_admin_function_permission->id
        ]);

        Permission::updateOrCreate([
            'code'      => 'edit_chat_channels',
            'name'      => 'edit chat channels',
            'parent_id' => $manage_chat_channels_permission->id
        ]);

        Permission::updateOrCreate([
            'code'      => 'delete_chat_channels',
            'name'      => 'delete chat channels',
            'parent_id' => $manage_chat_channels_permission->id
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::where('code', 'edit_user_permissions')->users()->detach()->delete();
        Permission::where('code', 'delete_users')->users()->detach()->delete();
        Permission::where('code', 'edit_users')->users()->detach()->delete();
        Permission::where('code', 'add_users')->users()->detach()->delete();
        Permission::where('code', 'manage_users')->users()->detach()->delete();
        Permission::where('code', 'access_admin_functions')->users()->detach()->delete();
    }
}
