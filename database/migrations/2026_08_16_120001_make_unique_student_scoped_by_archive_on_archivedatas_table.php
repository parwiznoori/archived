<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeUniqueStudentScopedByArchiveOnArchivedatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * ایندکس unique_student قبلاً بدون archive_id (شمارهٔ کتاب/آرشیف) تعریف شده بود
     * و فقط بر اساس
     * (name, father_name, grandfather_name, university_id, faculty_id, department_id)
     * بود. به همین دلیل ثبتِ همان محصل در «کتاب متفاوت» (مثلاً کتابِ فارغان
     * در مقابل کتابِ عادیِ همان پوهنځی) با خطای Duplicate entry برای
     * archivedatas.unique_student رد میشد.
     *
     * در این میگریشن ایندکس با archive_id (محدودهٔ یک کتاب) بازتعریف میشود تا
     * یکتایی فقط درونِ همان یک کتاب برقرار باشد؛ همان محصل در کتابهای مختلف مجاز است.
     */
    public function up()
    {
        Schema::table('archivedatas', function (Blueprint $table) {
            $table->dropUnique('unique_student');
        });

        Schema::table('archivedatas', function (Blueprint $table) {
            $table->unique(
                ['archive_id', 'name', 'father_name', 'grandfather_name', 'university_id', 'faculty_id', 'department_id'],
                'unique_student'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('archivedatas', function (Blueprint $table) {
            $table->dropUnique('unique_student');
        });

        Schema::table('archivedatas', function (Blueprint $table) {
            $table->unique(
                ['name', 'father_name', 'grandfather_name', 'university_id', 'faculty_id', 'department_id'],
                'unique_student'
            );
        });
    }
}