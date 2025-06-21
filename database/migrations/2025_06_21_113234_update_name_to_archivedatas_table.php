<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNameToArchivedatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('archivedatas', function (Blueprint $table) {
            $table->string('previous_name')->nullable()->after('name');
            $table->string('previous_father_name')->nullable()->after('father_name');
            $table->string('previous_grandfather_name')->nullable()->after('grandfather_name');
            $table->string('previous_birth_date')->nullable()->after('birth_date');
            $table->string('updateName_img')->nullable()->after('previous_name');
            $table->text('updateName_desc')->nullable()->after('updateName_img');
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
              $table->dropColumn([
                'previous_name',
                'previous_father_name',
                'previous_grandfather_name',
                'previous_birth_date',
                'updateName_img',
                'updateName_desc'
            ]);
        });
    }
}
