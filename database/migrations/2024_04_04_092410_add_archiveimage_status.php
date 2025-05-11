<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArchiveimageStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('archiveimages', function (Blueprint $table) {
            $table->integer('status_id')->unsigned()->index()->after('book_pagenumber');
        $table->foreign('status_id')
        ->references('id')
        ->on('archivedatastatus')
        ->onDelete('cascade');

        $table->integer('qc_status_id')->unsigned()->index()->default(1)->after('status_id');
        $table->foreign('qc_status_id')
            ->references('id')
            ->on('archiveqcstatus')
            ->onDelete('cascade');

        $table->integer('total_students')->unsigned()->nullable()->after('status_id');
        });
        Schema::enableForeignKeyConstraints();
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('archiveimages', function (Blueprint $table) {
            Schema::dropIfExists('total_students');

            $table->dropIndex(['status_id']);
            $table->dropForeign(['status_id']);
            $table->dropIndex(['qc_status_id']);
            $table->dropForeign(['qc_status_id']);

        });

        Schema::enableForeignKeyConstraints();
    }


}



      