<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExhibitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exhibits', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('case_id');
            $table->string('exhibit_catgory', 225)->nullable();
            $table->text('wildlife')->nullable();
            $table->text('implements')->nullable();
            $table->text('photos')->nullable();
            $table->text('description')->nullable();
            $table->bigInteger('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exhibits');
    }
}
