<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchiveUserEntryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archive_entry_users', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('role_id')->unsigned()->index();
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');

            $table->bigInteger('archive_id')->unsigned()->index();
            $table->foreign('archive_id')
                ->references('id')
                ->on('archives')
                ->onDelete('cascade');

            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->integer('status_id')->unsigned()->default(1)->index();
            $table->foreign('status_id')
                ->references('id')
                ->on('archivedatastatus')
                ->onDelete('cascade');

            $table->integer('qc_status_id')->unsigned()->default(1)->index();
            $table->foreign('qc_status_id')
                ->references('id')
                ->on('archiveqcstatus')
                ->onDelete('cascade');

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
        Schema::dropIfExists('archive_entry_users');
    }
}
