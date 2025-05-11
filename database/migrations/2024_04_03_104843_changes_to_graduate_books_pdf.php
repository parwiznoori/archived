<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangesToGraduateBooksPdf extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('graduate_books_pdf', function (Blueprint $table) {
            $table->dropColumn('viewPath');
            $table->dropColumn('statusMessage');
            $table->integer('generated_count')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('graduate_books_pdf', function (Blueprint $table) {
            $table->string('viewPath', 255)->nullable();
            $table->string('statusMessage', 512)->nullable();
            $table->dropColumn('generated_count');
        });
    }
}
