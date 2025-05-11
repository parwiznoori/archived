<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMonographColumnsToArchivedatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('archivedatas', function (Blueprint $table) {
            $table->string('monograph_doc_date')->nullable()->after('monograph_title');
            $table->string('monograph_doc_number')->nullable()->after('monograph_doc_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('archivedatas', function (Blueprint $table) {
            $table->dropColumn(['monograph_doc_date', 'monograph_doc_number']);

        });
    }
}
