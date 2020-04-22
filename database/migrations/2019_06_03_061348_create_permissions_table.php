<?php

use App\Models\Permission;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('permissions');
            $table->string('code');
            $table->string('name');
            $table->string('value')->nullable();
            $table->timestamps();
        });

        $permission = Permission::create([
            'code' => 'do_everything',
            'name' => 'do everything',
        ]);

        Schema::table('permissions', function($table) use($permission) {
            $table->unsignedBigInteger('parent_id')->nullable()->default($permission->id)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}
