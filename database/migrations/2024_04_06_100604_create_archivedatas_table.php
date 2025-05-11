<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchivedatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archivedatas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('archive_id')->unsigned()->index();
            $table->foreign('archive_id')
                ->references('id')
                ->on('archives')
                ->onDelete('cascade');
            

            $table->bigInteger('archiveimage_id')->unsigned()->index();
            $table->foreign('archiveimage_id')
            ->references('id')
            ->on('archiveimages')
            ->onDelete('cascade'); 

            $table->string('name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('father_name')->nullable();
            $table->string('grandfather_name')->nullable();
            $table->string('school')->nullable();
            $table->date('school_graduation_year');
            $table->string('tazkira_number')->nullable();
            $table->date('birth_date');
            $table->date('birth_place');
            $table->string('time')->nullable();
            $table->string('kankor_id')->unique(); 
            
            $table->integer('semester_type_id')->unsigned()->index(); 
            $table->foreign('semester_type_id')
            ->references('id')
            ->on('semester_type')
            ->onDelete('cascade'); 

            $table->date('year_of_inclusion');
            $table->date('graduated_year');
            $table->date('transfer_year')->nullable();
            $table->date('leave_year')->nullable();
            $table->date('failled_year')->nullable();
            $table->date('monograph_date');
            $table->string('monograph_number')->nullable();
            $table->string('monograph_title')->nullable();
            $table->string('averageOfScores')->nullable();

            $table->integer('grade_id')->unsigned()->index(); 
            $table->foreign('grade_id')
            ->references('id')
            ->on('grades')
            ->onDelete('cascade');

            $table->integer('status_id')->unsigned()->index()->default(1);
            $table->foreign('status_id')
                ->references('id')
                ->on('archivedatastatus')
                ->onDelete('cascade');

            $table->integer('qc_status_id')->unsigned()->default(1);
            $table->foreign('qc_status_id')
                ->references('id')
                ->on('archiveqcstatus')
                ->onDelete('cascade');
            
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('archivedatas');
    }
}
