<?php

Route::get('/', function () {
    return redirect(route('teacher.noticeboard.index'));
});

    Route::group(['namespace' => 'Teacher'], function () {
    Route::get('login', 'Auth\LoginController@showLoginForm');
    Route::post('login', 'Auth\LoginController@login')->name('teacher.login');
});

    Route::group(['middleware' => 'auth:teacher', 'as' => 'teacher.'], function() 
    {     
        Route::get('profile/password','ProfileController@index')->name('profile.password');
        Route::put('profile/password','ProfileController@store')->name('profile.password.store');
        
        Route::group(['namespace' => 'Teacher'], function () {
            
            Route::resource('/noticeboard', 'NoticeboardController')
                ->only('index', 'show')
                ->parameters(['noticeboard' => 'announcement']);        

            Route::get('timetable/course', 'Timetable\CourseController')->name('timetable.course');
            Route::post('timetable/course/list-by-year-and-half-year' , 'Timetable\CourseController@getListOfCoursesByYearAndSemester')->name('timetable.course.list-by-year-and-half-year');
            Route::get('timetable/course/{course}/list', 'AttendanceController@list')->name('timetable.course.list');
            Route::get('timetable/course/{course}/midterm', 'AttendanceController@midterm')->name('timetable.course.midterm');
            Route::get('timetable/course/{course}/final', 'AttendanceController@final_exam')->name('timetable.course.final');
            Route::get('timetable/course/{course}/chance2', 'AttendanceController@chance2_exam')->name('timetable.course.chance2');
            Route::get('timetable/course/{course}/chance3', 'AttendanceController@chance3_exam')->name('timetable.course.chance3');
            Route::get('timetable/course/{course}/chance4', 'AttendanceController@chance4_exam')->name('timetable.course.chance4');
            Route::get('timetable/course/{course}/all-chances', 'AttendanceController@all_chances_scores')->name('timetable.course.all_chances_scores');
            Route::get('timetable/course/{course}/deprived', 'AttendanceController@deprived_student')->name('timetable.course.deprived');
            Route::get('timetable/course/{course}/absent', 'AttendanceController@absent_student')->name('timetable.course.absent');
            Route::get('timetable/course/{course}/excuse', 'AttendanceController@excuse_exam')->name('timetable.course.excuse');
            Route::get('timetable/course/{course}/chance1-approved', 'AttendanceController@chance1_approved')->name('course.chance1_approved');
            Route::post('timetable/course/{course}/approved_chance1_by_teacher', 'AttendanceController@approved_chance1_by_teacher')->name('course.approved_chance1_by_teacher'); 
             
            Route::get('courses/{course}/import-excel/chance1', 'AttendanceController@import_chance1_from_excel')->name('import.import_chance1_from_excel');
            Route::post('courses/{course}/import-excel/chance1', 'AttendanceController@score_chance1_from_excel')->name('import.score_chance1_from_excel');

            Route::get('support', function () {
                return view('support', [
                    'title' => trans('general.support')
                ]);
            })->name('support');

           
        });

        Route::get('/locale/{locale}', function ($locale) {
            
            \Session::put('locale' , $locale);

            return redirect()->back();
        })->name('locale');

        Route::post('timetable/course/{course}/scores', 'Course\ScoresController')->name('scores.store');
        Route::post('timetable/course/{course}/store_midtrem', 'Course\ScoresController@store_midtrem')->name('scores.store_midtrem');
        Route::post('timetable/course/{course}/store_final', 'Course\ScoresController@store_final')->name('scores.store_final');
        Route::post('timetable/course/{course}/store_excuse', 'Course\ScoresController@store_excuse')->name('scores.store_excuse');
        Route::post('timetable/course/{course}/store_chance_two', 'Course\ScoresController@store_chance_two')->name('scores.store_chance_two');
        Route::post('timetable/course/{course}/store_chance_three', 'Course\ScoresController@store_chance_three')->name('scores.store_chance_three');
        Route::post('timetable/course/{course}/store_chance_four', 'Course\ScoresController@store_chance_four')->name('scores.store_chance_four');
        Route::post('timetable/course/{course}/store_deprived', 'Course\ScoresController@store_deprived')->name('scores.store_deprived');
        Route::post('timetable/course/{course}/update_deprived', 'Course\ScoresController@update_deprived')->name('scores.update_deprived');
        Route::post('timetable/course/{course}/store_absent_exam', 'Course\ScoresController@store_absent_exam')->name('scores.store_absent_exam');
        Route::post('timetable/course/{course}/update_absent_exam', 'Course\ScoresController@update_absent_exam')->name('scores.update_absent_exam');
        Route::get('courses/{course}/scores-sheet', 'Course\ScoreSheetController@print')->name('course.scoresSheet.print');

        Route::get('courses/{course}/scores-sheet/chance1', 'Course\ScoreSheetController@print_chance1')->name('course.scoresSheet.print_chance1');
        Route::get('courses/{course}/scores-sheet/chance2', 'Course\ScoreSheetController@print_chance2')->name('course.scoresSheet.print_chance2');
        Route::get('courses/{course}/scores-sheet/chance3', 'Course\ScoreSheetController@print_chance3')->name('course.scoresSheet.print_chance3');
        Route::get('courses/{course}/scores-sheet/chance4', 'Course\ScoreSheetController@print_chance4')->name('course.scoresSheet.print_chance4');
        Route::get('courses/{course}/export-excel/chance1', 'Course\ScoreSheetController@export_excel_chanc1')->name('course.scoresSheet.export-excel-chance1');

    });