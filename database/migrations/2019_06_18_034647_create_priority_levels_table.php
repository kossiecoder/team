<?php

use App\Models\PriorityLevel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriorityLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('priority_levels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('theme')->nullable();
            $table->string('code');
            $table->timestamps();
        });

        PriorityLevel::create([
            'name'  => 'Low',
            'theme' => 'info',
            'code'  => 'low'
        ]);
        PriorityLevel::create([
            'name'  => 'Normal',
            'theme' => 'success',
            'code'  => 'normal'
        ]);

        PriorityLevel::create([
            'name'  => 'High',
            'theme' => 'warning',
            'code'  => 'high'
        ]);
        PriorityLevel::create([
            'name'  => 'Urgent',
            'theme' => 'error',
            'code'  => 'urgent'
        ]);
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('priority_levels');
    }
}
