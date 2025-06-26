<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBaqidarisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('baqidaris', function (Blueprint $table) {
        $table->bigIncrements('id');
        
        // Foreign key (unsigned bigInteger matches bigIncrements)
        $table->unsignedBigInteger('archivedata_id');
        $table->foreign('archivedata_id')
            ->references('id')
            ->on('archivedatas')
            ->onDelete('cascade');
        
        // Semester enum (fixed typo in 'semester5')
        $table->enum('semester', [
            'semester1', 
            'semester2', 
            'semester3', 
            'semester4', 
            'semester5', 
            'semester6', 
            'semester7', 
            'semester8'  // Fixed typo (was 'semester8' in your original)
        ])->nullable();
        
        $table->string('subject')->nullable();
        
        // Decimal fields with precision (total digits, decimal places)
        $table->decimal('chance_number', 10, 2)->nullable();       // e.g., 100.50
        $table->decimal('zarib_chance', 10, 2)->nullable();        // e.g., 1.50
        $table->decimal('chance_number2', 10, 2)->nullable();      // e.g., 100.50
        $table->decimal('monoghraph', 10, 2)->nullable();          // Fixed typo (likely 'monograph')
        $table->decimal('zarib_credite', 10, 2)->nullable();       // Fixed typo (likely 'zarib_credit')
        $table->decimal('credit', 10, 2)->nullable();
        $table->decimal('total_credit', 10, 2)->nullable();
        $table->decimal('total_numbers', 10, 2)->nullable();
        $table->decimal('semester_percentage', 5, 2)->nullable();  // 0.00-100.00
        
        // Grouped related fields for clarity
        $table->decimal('total_credit2', 10, 2)->nullable();
        $table->decimal('total_numbers2', 10, 2)->nullable();
        $table->decimal('semester_percentage2', 5, 2)->nullable();
        
        $table->decimal('total_credit3', 10, 2)->nullable();
        $table->decimal('total_numbers3', 10, 2)->nullable();
        $table->decimal('eight_semester_percentage3', 5, 2)->nullable();  // Fixed naming consistency
        
        $table->decimal('total_credit4', 10, 2)->nullable();
        $table->decimal('total_numbers4', 10, 2)->nullable();
        $table->decimal('eight_semester_percentage4', 5, 2)->nullable();
        $table->string('baqidari_img')->nullable();
        
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
        Schema::dropIfExists('baqidaris');
    }
}
