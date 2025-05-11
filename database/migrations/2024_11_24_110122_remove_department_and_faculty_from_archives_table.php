<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveDepartmentAndFacultyFromArchivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('archives', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['department_id']);
            $table->dropForeign(['faculty_id']);

            // Drop the columns
            $table->dropColumn(['department_id', 'faculty_id']);
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
            // Re-add the columns
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('faculty_id')->nullable();

            // Re-add the foreign key constraints
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('cascade');
        });
    }
}
