<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFacultyAndDepartmentToArchivedatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('archivedatas', function (Blueprint $table) {

            $table->integer('university_id')->unsigned()->nullable()->index();
            $table->foreign('university_id')
                ->references('id')
                ->on('universities')
                ->onDelete('cascade');

            $table->integer('faculty_id')->unsigned()->nullable()->index();
                $table->foreign('faculty_id')
                    ->references('id')
                    ->on('faculties')
                    ->onDelete('cascade');

                $table->integer('department_id')->unsigned()->nullable()->index();
                $table->foreign('department_id')
                    ->references('id')
                    ->on('departments')
                    ->onDelete('cascade');

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
            $table->dropForeign(['university_id']);
            $table->dropColumn('university_id');

            $table->dropForeign(['faculty_id']);
            $table->dropColumn('faculty_id');

            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });
    }
}
