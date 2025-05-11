<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Student;

class AddFeildsToStudents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken()->after('password');
            $table->boolean('active')->default(true);
        });

        \DB::table('students')->whereIn('kankor_year', [1396,1397,1398,1399])->update([
            'password' => bcrypt('password')]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            
            $table->dropColumn('email');
            $table->dropColumn('password');
            $table->dropColumn('remember_');
            $table->dropColumn('active');
        });
    }
}
