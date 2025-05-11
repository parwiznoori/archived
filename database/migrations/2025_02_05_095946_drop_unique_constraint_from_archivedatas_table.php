<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUniqueConstraintFromArchivedatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('archivedatas', function (Blueprint $table) {
            // Drop the unique constraint
            $table->dropUnique('archivedatas_kankor_id_unique'); // Use the correct index name
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
            // Re-add the unique constraint in the down method
            $table->unique('kankor_id', 'archivedatas_kankor_id_unique'); // Use the same index name
        });
    }
}
