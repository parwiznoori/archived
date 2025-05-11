<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldPermanentToDropouts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dropouts', function (Blueprint $table) {
            $table->boolean('permanent')->default(0)->after('university_id')->comment('1 for permanent dropout');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dropouts', function (Blueprint $table) {
            $table->dropIfExists(['permanent']);
        });
    }
}
