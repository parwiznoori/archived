<?php

Route::get('/support', function () {
    // dd(auth('student')->user()->id);
      return redirect(route('student.support'));
});

Route::group(['namespace' => 'Students'], function () {
    
    Route::get('login', 'Auth\LoginController@showLoginForm');
    Route::post('login', 'Auth\LoginController@login')->name('student.login'); 
    Route::get('porfile/{student}/student-form' , 'StudentFormsController@index')->name('profile.student.form')->middleware('auth:student');
    Route::post('profile/{student}/generate-form' , 'StudentFormsController@generateForm')->name('profile.student.generate-form')->middleware('auth:student');
    Route::get('timetable/course', 'Timetable\CourseController')->name('student.timetable.course')->middleware('auth:student');

});

Route::group(['middleware' => 'auth:student', 'as' => 'student.'], function() {     
    Route::get('profile/password','ProfileController@index')->name('profile.password');
    Route::put('profile/password','ProfileController@store')->name('profile.password.store');
    Route::get('profile', 'Students\AuthStudent\StudentController@index')->name('profile');
    Route::get('information', 'Students\AuthStudent\StudentController@information')->name('information');
    Route::get('group', 'Students\AuthStudent\StudentController@group')->name('group');
    Route::get('leave', 'Students\AuthStudent\StudentController@leave')->name('leave');
    Route::get('dropout', 'Students\AuthStudent\StudentController@dropout')->name('dropout');
    Route::get('transfer', 'Students\AuthStudent\StudentController@transfer')->name('transfer');
    Route::get('courses-list', 'Students\AuthStudent\StudentController@coursesList')->name('courses-list');
    Route::get('scores-list', 'Students\AuthStudent\StudentController@scoresList')->name('scores-list');
    Route::get('final-scores-list', 'Students\AuthStudent\StudentController@finalScoresList')->name('final-scores-list');
    // Route::get('student/{student}/scores','Students\TranscriptController@displayStudentScores')->name('scores');
    // Route::get('student/{student}/allDetail','Students\AuthStudent\allDetailController@index')->name('allDetail');
    //  Route::get('student/{student}/leave','Students\AuthStudent\allDetailController@leave')->name('leave');
    //  Route::get('student/{student}/groupandclass','Students\AuthStudent\allDetailController@group')->name('groupandclass');
});

Route::get('support', function () {
    return view('support', [
        'title' => trans('general.support')
    ]);
})->name('student.support')->middleware('auth:student');


Route::get('student/locale/{locale}', function ($locale) {

    \Session::put('locale' , $locale);

    return redirect()->back();
})->name('student.locale');

// Route to print student form
