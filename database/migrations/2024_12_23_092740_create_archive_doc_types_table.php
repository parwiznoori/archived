<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchiveDocTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archive_doc_types', function (Blueprint $table) {
            // Primary key (auto-incrementing ID)
            $table->bigIncrements('id');

            // Foreign key column (archivedata_id)
            $table->bigInteger('archivedata_id')->unsigned();
            $table->foreign('archivedata_id')
                ->references('id')
                ->on('archivedatas')
                ->onDelete('cascade');

            // Other columns
            $table->string('doc_type');
            $table->string('doc_number')->nullable();
            $table->string('doc_file')->nullable();
            $table->string('doc_description')->nullable();

            // Timestamps
            $table->timestamps();

            // Composite unique key on archivedata_id and doc_type
            $table->unique(['archivedata_id', 'doc_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop foreign key constraint first before dropping the table
        Schema::table('archive_doc_types', function (Blueprint $table) {
            $table->dropForeign(['archivedata_id']);
        });

        // Drop the table
        Schema::dropIfExists('archive_doc_types');
    }
}
