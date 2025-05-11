<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRemovalDropoutAndDescriptionToDropouts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dropouts', function (Blueprint $table) {
            $table->boolean('removal_dropout')->default(0)->after('permanent');
            $table->text('removal_dropout_description')->after('removal_dropout');
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
            $table->dropIfExists(['removal_dropout']);
            $table->dropIfExists(['removal_dropout_description']);
        });
    }
}
