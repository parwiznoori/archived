<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploadedCsvLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploaded_csv_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('archivedata_id')->unsigned();
            $table->foreign('archivedata_id')
                ->references('id')
                ->on('archivedatas')
                ->onDelete('cascade');
            $table->string('batch_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uploaded_csv_logs');
    }
}
