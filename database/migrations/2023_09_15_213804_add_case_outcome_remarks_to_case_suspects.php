<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCaseOutcomeRemarksToCaseSuspects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('case_suspects', function (Blueprint $table) {
            $table->mediumText('case_outcome_remarks')->nullable()->after('case_outcome');
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
            $table->dropColumn('case_outcome_remarks');
        });
    }
}
