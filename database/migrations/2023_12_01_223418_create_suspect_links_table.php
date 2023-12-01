<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuspectLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suspect_links', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer("suspect_id_1");
            $table->integer("suspect_id_2");
            $table->integer("case_id_1");
            $table->integer("case_id_2");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suspect_links');
    }
}
