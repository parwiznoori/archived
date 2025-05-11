<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAndValidationUniversities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->string('name_eng')->nullable(false)->change();
            $table->unique('name');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('name_eng')->nullable()->change();
            $table->dropUnique(['name']);
            
        });
    }
}
