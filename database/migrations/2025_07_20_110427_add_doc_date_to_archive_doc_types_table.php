<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocDateToArchiveDocTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('archive_doc_types', function (Blueprint $table) {
         $table->date('doc_date')->nullable()->after('doc_type');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('archive_doc_types', function (Blueprint $table) {
             $table->dropColumn('doc_date');
        });
    }
}
