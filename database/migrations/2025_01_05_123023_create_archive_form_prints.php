<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchiveFormPrints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archive_form_prints', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('archive_form_temp_id')->unsigned();
            $table->foreign('archive_form_temp_id')
                ->references('id')
                ->on('archive_form_templates')
                ->onDelete('cascade');

            $table->text('content')->nullable();

            $table->bigInteger('archivedata_id')->unsigned();
            $table->foreign('archivedata_id')
                ->references('id')
                ->on('archivedatas')
                ->onDelete('cascade');
            $table->timestamps();
            $table->unique(['archivedata_id', 'archive_form_temp_id']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('archive_form_prints');
    }
}
