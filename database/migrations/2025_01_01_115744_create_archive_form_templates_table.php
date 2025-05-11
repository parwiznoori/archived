<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchiveFormTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archive_form_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('form_name')->nullable();
            $table->text('content')->nullable();
            $table->boolean('status')->default(true);
            $table->string('variable')->nullable();
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
        Schema::dropIfExists('archive_form_templates');
    }
}
