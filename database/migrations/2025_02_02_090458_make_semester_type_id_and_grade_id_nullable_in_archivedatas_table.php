<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeSemesterTypeIdAndGradeIdNullableInArchivedatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('archivedatas', function (Blueprint $table) {

                // Make semester_type_id nullable
                $table->unsignedInteger('semester_type_id')->nullable()->change();

                // Make grade_id nullable
                $table->unsignedInteger('grade_id')->nullable()->change();

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
            $table->unsignedInteger('semester_type_id')->nullable(false)->change();

            // Revert grade_id to not nullable
            $table->unsignedInteger('grade_id')->nullable(false)->change();
        });
    }
}
