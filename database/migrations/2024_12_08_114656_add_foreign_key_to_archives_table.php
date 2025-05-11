<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToArchivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('archives', function (Blueprint $table) {
            // Ensure 'archive_year_id' is an unsignedBigInteger to match the 'id' in 'archiveyears'
            $table->bigInteger('archive_year_id')->unsigned()->nullable()->index()->after('book_name');
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
        Schema::table('archives', function (Blueprint $table) {
            $table->dropIndex(['archive_year_id']);
            $table->dropForeign(['archive_year_id']);
        });
    }
}
