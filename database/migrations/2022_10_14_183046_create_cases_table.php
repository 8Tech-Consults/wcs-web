<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('reported_by');
            $table->string('latitude', 225)->nullable();
            $table->string('longitude', 225)->nullable();
            $table->bigInteger('district_id');
            $table->bigInteger('sub_county_id');
            $table->string('parish', 225)->nullable();
            $table->string('village', 225)->nullable();
            $table->bigInteger('offence_category_id');
            $table->text('offence_description');
            $table->boolean('is_offence_committed_in_pa');
            $table->bigInteger('pa_id')->nullable();
            $table->boolean('has_exhibits');
            $table->integer('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cases');
    }
}
