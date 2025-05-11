<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBookPagenumberInArchivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('archives', function (Blueprint $table) {
            // Change the default value of book_pagenumber to null
            $table->integer('book_pagenumber')->nullable()->default(null)->change();
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
            // Revert the column back to its previous state if necessary
            $table->integer('book_pagenumber')->nullable(false)->default(0)->change(); // Or whatever the previous state was
        });
    }
}


