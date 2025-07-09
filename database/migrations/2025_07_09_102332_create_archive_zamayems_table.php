<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchiveZamayemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archive_zamayems', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('archivedata_id');
            $table->foreign('archivedata_id')
            ->references('id')
            ->on('archivedatas')
            ->onDelete('cascade');


            $table->string('title')->nullable(); 
            $table->string('zamayem_img')->nullable();
   
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
        Schema::dropIfExists('archive_zamayems');
    }
}
