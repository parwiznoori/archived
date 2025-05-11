<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchiveimagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archiveimages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('archive_id')->unsigned()->index();
            $table->foreign('archive_id')
                ->references('id')
                ->on('archives')
                ->onDelete('cascade');
                
            $table->string('path')->nullable();
            $table->integer('book_pagenumber')->nullable(); 


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
        Schema::dropIfExists('archiveimages');
    }
}
