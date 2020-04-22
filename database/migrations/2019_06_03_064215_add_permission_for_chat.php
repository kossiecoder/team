<?php

use App\Models\Permission;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPermissionForChat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::updateOrCreate([
            'code' => 'add_chat_channels',
            'name' => 'add chat channels'
        ]);

        Permission::updateOrCreate([
            'code'      => 'edit_own_chat_channels',
            'name'      => 'edit own chat channels'
        ]);

        Permission::updateOrCreate([
            'code'      => 'delete_own_chat_channels',
            'name'      => 'delete own chat channels'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::where('code', 'add_chat_channels')->users()->detach()->delete();
        Permission::where('code', 'edit_own_chat_channels')->users()->detach()->delete();
        Permission::where('code', 'delete_own_chat_channels')->users()->detach()->delete();
    }
}
