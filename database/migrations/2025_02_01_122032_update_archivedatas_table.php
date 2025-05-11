<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateArchivedatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('archivedatas', function (Blueprint $table) {

            $table->string('last_name')->nullable()->change();
            $table->string('grandfather_name')->nullable()->change();
            $table->string('school')->nullable()->change();
            $table->string('school_graduation_year')->nullable()->change();
            $table->string('tazkira_number')->nullable()->change();
            $table->date('birth_date')->nullable()->change();
            $table->string('birth_place')->nullable()->change();
            $table->string('time')->nullable()->change();
            $table->string('kankor_id')->nullable()->change();
            $table->string('transfer_year')->nullable()->change();
            $table->string('leave_year')->nullable()->change();
            $table->string('failled_year')->nullable()->change();
            $table->string('monograph_date')->nullable()->change();
            $table->string('monograph_number')->nullable()->change();
            $table->string('monograph_title')->nullable()->change();
            $table->decimal('averageOfScores', 5, 2)->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->string('column_number')->nullable()->change();
            $table->date('monograph_doc_date')->nullable()->change();
            $table->string('monograph_doc_number')->nullable()->change();
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
            // Revert changes if needed
        });
    }
}
