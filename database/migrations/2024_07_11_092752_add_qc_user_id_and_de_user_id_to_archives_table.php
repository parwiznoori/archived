<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQcUserIdAndDeUserIdToArchivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('archives', function (Blueprint $table) {
            $table->integer('qc_user_id')->nullable()->after('qc_status_id');
            $table->integer('de_user_id')->nullable()->after('qc_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('archives', function (Blueprint $table) {
            $table->dropIfExists(['qc_user_id']);
            $table->dropIfExists(['de_user_id']);
        });
    }
}
