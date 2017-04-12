<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('positions', function (Blueprint $table) {
          $table->string('user_id');
          $table->string('username');
          $table->string('type');
          $table->string('class');
          $table->integer('old_position');
          $table->integer('given_points')->nullable();
          $table->integer('received_points')->nullable();
          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('positions', function (Blueprint $table) {
          Schema::dropIfExists('positions');
        });
    }
}
