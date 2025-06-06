<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldDeprivedToScores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->boolean('deprived')->default(0)->after('passed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dropColumn('deprived');
        });
    }
}
