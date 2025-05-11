<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAllDateColumnTypeInArchivedatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('archivedatas', function (Blueprint $table) {
            $table->string('school_graduation_year')->change();
            $table->string('birth_date')->change();
            $table->string('birth_place')->change();
            $table->string('year_of_inclusion')->change();
            $table->string('graduated_year')->change();
            $table->string('transfer_year')->change();
            $table->string('leave_year')->change();
            $table->string('failled_year')->change();
            $table->float('monograph_number')->change();
            $table->float('averageOfScores')->change();
            $table->text('description')->change();
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
            // Revert the column type changes back to date
            $table->date('school_graduation_year')->change();
            $table->date('birth_date')->change();
            $table->date('birth_place')->change();
            $table->date('year_of_inclusion')->change();
            $table->date('graduated_year')->change();
            $table->date('transfer_year')->change();
            $table->date('leave_year')->change();
            $table->date('failled_year')->change();
            $table->string('monograph_number')->change();
            $table->string('averageOfScores')->change();
            $table->string('description')->change();
        });
    }
}
