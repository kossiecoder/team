<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('creator_id');
            $table->unsignedBigInteger('priority_level_id')->nullable();
            $table->unsignedBigInteger('assignee_id')->nullable();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('creator_id')->references('id')->on('users');
            $table->foreign('assignee_id')->references('id')->on('users');
            $table->foreign('priority_level_id')->references('id')->on('priority_levels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
