<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreationRequestKeyToArchives extends Migration
{
    public function up()
    {
        Schema::table('archives', function (Blueprint $table) {
            $table->char('creation_request_key', 64)->nullable()->unique();
        });
    }

    public function down()
    {
        Schema::table('archives', function (Blueprint $table) {
            $table->dropUnique(['creation_request_key']);
        });
        Schema::table('archives', function (Blueprint $table) {
            $table->dropColumn('creation_request_key');
        });
    }
}
