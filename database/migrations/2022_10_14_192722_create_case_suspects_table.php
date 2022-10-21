<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaseSuspectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('case_suspects', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('case_id');
            $table->string('uwa_suspect_number', 225);
            $table->string('first_name', 225)->nullable();
            $table->string('middle_name', 225)->nullable();
            $table->string('last_name', 225)->nullable();
            $table->string('phone_number', 225)->nullable();
            $table->string('national_id_number', 225)->nullable();
            $table->string('sex', 10)->nullable();
            $table->integer('age')->nullable();
            $table->string('occuptaion', 225)->nullable();
            $table->string('country', 225)->nullable();
            $table->integer('district_id')->nullable();
            $table->integer('sub_county_id')->nullable();
            $table->string('parish', 225)->nullable();
            $table->string('village', 225)->nullable();
            $table->string('ethnicity', 225)->nullable();
            $table->text('finger_prints')->nullable();
            $table->boolean('is_suspects_arrested')->nullable();
            $table->dateTime('arrest_date_time')->nullable();
            $table->integer('arrest_district_id')->nullable();
            $table->integer('arrest_sub_county_id')->nullable();
            $table->string('arrest_parish', 225)->nullable();
            $table->string('arrest_village', 225)->nullable();
            $table->string('arrest_latitude', 225)->nullable();
            $table->string('arrest_longitude', 225)->nullable();
            $table->string('arrest_first_police_station', 225)->nullable();
            $table->string('arrest_current_police_station', 225)->nullable();
            $table->string('arrest_agency', 225)->nullable();
            $table->string('arrest_uwa_unit', 225)->nullable();
            $table->string('arrest_detection_method', 225)->nullable();
            $table->string('arrest_uwa_number', 225)->nullable();
            $table->string('arrest_crb_number', 225)->nullable();
            $table->boolean('is_suspect_appear_in_court')->nullable();
            $table->string('prosecutor', 225)->nullable();
            $table->boolean('is_convicted')->nullable();
            $table->text('case_outcome')->nullable();
            $table->text('magistrate_name')->nullable();
            $table->text('court_name')->nullable();
            $table->text('court_file_number')->nullable();
            $table->boolean('is_jailed')->nullable();
            $table->integer('jail_period')->nullable();
            $table->boolean('is_fined')->nullable();
            $table->integer('fined_amount')->nullable();
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
        Schema::dropIfExists('case_suspects');
    }
}
