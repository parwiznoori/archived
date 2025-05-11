<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeBirthDateAndMonographDocDateToString extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('archivedatas', function (Blueprint $table) {
            $table->string('birth_date')->change(); // Change to string
            $table->string('monograph_doc_date')->change(); // Change to string
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('archivedatas', function (Blueprint $table) {
            $table->date('birth_date')->change(); // Revert to date
            $table->date('monograph_doc_date')->change(); // Revert to date
        });
    }
}
