<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuspectHasOffencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suspect_has_offences', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('suspect_id');
            $table->bigInteger('offence_id');
            $table->string('vadict')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suspect_has_offences');
    }
}
