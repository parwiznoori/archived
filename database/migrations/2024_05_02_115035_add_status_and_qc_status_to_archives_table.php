<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusAndQcStatusToArchivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('archives', function (Blueprint $table) {
            $table->integer('status_id')->unsigned()->index()->default(1)->after('book_description');
            $table->foreign('status_id')
                ->references('id')
                ->on('archivedatastatus')
                ->onDelete('cascade');

            $table->integer('qc_status_id')->unsigned()->index()->default(1)->after('status_id');
            $table->foreign('qc_status_id')
                ->references('id')
                ->on('archiveqcstatus')
                ->onDelete('cascade');

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
        Schema::table('archives', function (Blueprint $table) {
            Schema::dropIfExists('qc_status');
            $table->dropIndex(['status_id']);
            $table->dropForeign(['status_id']);
            $table->dropIndex(['qc_status_id']);
            $table->dropForeign(['qc_status_id']);

        });
        Schema::enableForeignKeyConstraints();
    }
}
