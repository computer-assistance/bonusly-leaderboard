<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePositionChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('position_changes', function (Blueprint $table) {
          $table->string('user_id');
          $table->integer('old_position');
          $table->integer('new_position');
          $table->timestamps();
          $table->primary('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('position_changes', function (Blueprint $table) {
          Schema::dropIfExists('position_changes');
        });
    }
}
