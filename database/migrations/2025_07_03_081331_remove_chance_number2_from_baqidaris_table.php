<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveChanceNumber2FromBaqidarisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('baqidaris', function (Blueprint $table) {
             $table->dropColumn('chance_number2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('baqidaris', function (Blueprint $table) {
            $table->decimal('chance_number2', 50)->nullable();
        });
    }
}
