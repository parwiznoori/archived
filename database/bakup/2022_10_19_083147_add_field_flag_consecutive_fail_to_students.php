<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldFlagConsecutiveFailToStudents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->tinyInteger('flag_consecutive_fail')->default(0)->comment('اگر محصل در دو سمستر پشت سر هم ، در بیشتر از 50 فیصد کردیتهای همان سمستر ناکام شود.محصل باید سال تحصیلی را تکرار کند.
            پیش فرض این فیلد 0 است.
            اگر مقدار 1 شود یعنی در سمستر فعلی ناکام شده است.
            اگر مقدار ان 2 شود به این معنی است که محصل دو سمستر پشت سر هم ناکام شده است و باعث منفکی دایمی محصل میشود.
            در هر سمستر بعد از ایجاد نتایج سمستر وار این فیلد چک میشود. در صورت کامیابی مقدار ام 0 میشود.در صئرت ناکلمی مقدار ان یک واحد اضافه میشود. ');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('flag_consecutive_fail');
        });
    }
}
