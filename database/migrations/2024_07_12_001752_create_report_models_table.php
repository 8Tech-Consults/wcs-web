<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_models', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('title')->nullable();
            $table->integer('cases_count')->nullable();
            $table->integer('suspects_count')->nullable();
            $table->integer('exhibits_count')->nullable();
            $table->string('type')->nullable();
            $table->string('ca_id')->nullable();
            $table->string('pa_id')->nullable();
            $table->string('date_type')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('is_generated')->nullable();
            $table->date('date_generated')->nullable();
            $table->string('generated_by_id')->nullable();
            $table->text('pdf_file')->nullable();
            $table->integer('downloads')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_models');
    }
}
