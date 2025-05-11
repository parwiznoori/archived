<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegisterationOfGraduateDocumentsFeildsToGraduatedStudents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('graduated_students', function (Blueprint $table) {

            $table->date('registeration_date')->nullable();
            $table->boolean('received_diploma')->default(0)->nullable();
            $table->date('received_diploma_date')->nullable();
            $table->string('diploma_letter_number')->nullable();
            $table->date('diploma_letter_date')->nullable();
            $table->string('diploma_number')->nullable();
            $table->boolean('received_certificate')->default(0)->nullable();
            $table->date('received_certificate_date')->nullable();
            $table->string('certificate_letter_number')->nullable();
            $table->date('certificate_letter_date')->nullable();
            $table->boolean('received_transcript_en')->default(0)->nullable();
            $table->date('received_transcript_en_date')->nullable();
            $table->string('transcript_en_letter_number')->nullable();
            $table->date('transcript_en_letter_date')->nullable();
            $table->boolean('received_transcript_da')->default(0)->nullable();
            $table->date('received_transcript_da_date')->nullable();
            $table->string('transcript_da_letter_number')->nullable();
            $table->date('transcript_da_letter_date')->nullable();
            $table->boolean('received_transcript_pa')->default(0)->nullable();
            $table->date('received_transcript_pa_date')->nullable();
            $table->string('transcript_pa_letter_number')->nullable();
            $table->date('transcript_pa_letter_date')->nullable();
            $table->boolean('hand_over_identity_card')->default(0)->nullable();
            $table->boolean('hand_over_non_responsibility_form')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('graduated_students', function (Blueprint $table) {

            $table->dropIfExists(['registeration_date']);
            $table->dropIfExists(['received_diploma']);
            $table->dropIfExists(['received_diploma_date']);
            $table->dropIfExists(['diploma_letter_number']);
            $table->dropIfExists(['diploma_letter_date']);
            $table->dropIfExists(['diploma_number']);
            $table->dropIfExists(['received_certificate']);
            $table->dropIfExists(['received_certificate_date']);
            $table->dropIfExists(['certificate_letter_number']);
            $table->dropIfExists(['certificate_letter_date']);
            $table->dropIfExists(['received_transcript_en']);
            $table->dropIfExists(['received_transcript_en_date']);
            $table->dropIfExists(['transcript_en_letter_number']);
            $table->dropIfExists(['transcript_en_letter_date']);
            $table->dropIfExists(['received_transcript_da']);
            $table->dropIfExists(['received_transcript_da_date']);
            $table->dropIfExists(['transcript_da_letter_number']);
            $table->dropIfExists(['transcript_da_letter_date']);
            $table->dropIfExists(['received_transcript_pa']);
            $table->dropIfExists(['received_transcript_pa_date']);
            $table->dropIfExists(['transcript_pa_letter_number']);
            $table->dropIfExists(['transcript_pa_letter_date']);
            $table->dropIfExists(['hand_over_identity_card']);
            $table->dropIfExists(['hand_over_non_responsibility_form']);
            
        });
    }
}
