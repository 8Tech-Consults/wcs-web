<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtherArrestAgenciesToCaseSuspectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('case_suspects', function (Blueprint $table) {
            $table->json('other_arrest_agencies')->nullable()->after('arrest_agency');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('case_suspects', function (Blueprint $table) {
            //
        });
    }
}
