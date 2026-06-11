<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchiveArchiveYearTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archive_archive_year', function (Blueprint $table) {
              $table->bigIncrements('id'); // Primary key

            $table->bigInteger('archive_id')->unsigned()->index();
            $table->foreign('archive_id')
                ->references('id')
                ->on('archives')
                ->onDelete('cascade');

            $table->bigInteger('archive_year_id')->unsigned()->index();
            // Add the foreign key constraint
            $table->foreign('archive_year_id')
                ->references('id')
                ->on('archiveyears')
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
        Schema::dropIfExists('archive_archive_year');
    }
}
