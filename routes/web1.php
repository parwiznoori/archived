<?php


Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

Route::group(['middleware' => 'auth'], function() { 
    Route::get('profile/password','ProfileController@index')->name('profile.password');
    Route::put('profile/password','ProfileController@store')->name('profile.password.store');

    Route::impersonate();

    Route::group(['namespace' => 'Api'], function() { 
        Route::get('api/students', "StudentsController")->name('api.students');// Filter is on Model
        Route::get('api/provinces', "ProvinceController")->name('api.provinces');
        Route::get('api/subjects/{department?}', "SubjectsController")->name('api.subjects');// Filter is on Model
        Route::get('api/groups/{department?}', "GroupsController")->name('api.groups');// Filter is on Model
        Route::get('api/teachers{university?}', "TeachersController")->name('api.teachers');// Filter is on Model
        Route::get('api/departments/{university?}', "DepartmentsController")->name('api.departments');
        Route::get('api/grades', "GradesController")->name('api.grades');
    });
    
    Route::get('home/{kankor_year?}', 'HomeController@index')->name('home');
    
    Route::get('/support', function () {
        return view('support', [
            'title' => trans('general.support')
        ]);
    })->name('support');

    Route::group(['namespace' => 'Users'], function() { 
        Route::resource('/users', 'UsersController');
        Route::resource('/roles', 'RolesController');
    });

    Route::group(['namespace' => 'Students'], function() {
        Route::get('student/groups/create-all','Groups\CreateGroupsController@index')->name('student.groups.all.create');
        Route::post('student/groups/create-all','Groups\CreateGroupsController@store')->name('student.groups.all.store');

        Route::resource('/students/groups', 'Groups\GroupsController');
        Route::get('/students/groups/{group}/list', 'Groups\GroupListController@index')->name('groups.list');
        Route::post('/students/groups/{group}/list', 'Groups\GroupListController@addStudent')->name('groups.student.add');
        Route::delete('/students/groups/{group}/list', 'Groups\GroupListController@removeStudent')->name('groups.student.remove');
        Route::get('student/groups/groups-automation','Groups\GroupsAutomationController@index')->name('student.groups.automation');
        Route::post('student/groups/groups-automation/generate','Groups\GroupsAutomationController@store')->name('student.groups.automation.generate');

        Route::resource('/students', 'StudentsController');
        Route::patch('/students/{student}/updateStatus', 'StudentsController@updateStatus')->name('students.updateStatus');
        Route::get('/students/{student}/card', 'StudentCardController@index')->name('students.card');
        Route::get('/students/{student}/photo', 'PhotoController@index')->name('students.photo');
        Route::put('/students/{student}/photo', 'PhotoController@store')->name('students.photo.update');

        Route::get('{student}/downloads', 'StudentDownloadController@index')->name('students.downloads');
        Route::get('{student}/downloads/{file}', 'StudentDownloadController@show')->name('students.downloads.download');

        Route::resource('/transfers', 'TransfersController');
        Route::resource('/dropouts', 'DropoutsController');
        Route::resource('/leaves', 'LeavesController', ['parameters' => [
            'leaves' => 'leave'
        ]]);
        Route::get('leaves/{leave}/end-leave' , 'EndLeaveController@index')->name('leaves.end_leave');

        //students forms
        Route::get('students/{student}/student-form' , 'StudentFormsController@index')->name('student.form');
        Route::post('students/{student}/generate-form' , 'StudentFormsController@generateForm')->name('student.generate-form');

        Route::get('students/‌semester-base/result', 'StudentsResultController@index')->name('students.semester-base.result');
        Route::Post('students/semester-base/result/create', 'StudentsResultController@create')->name('students.semester-base.result.create');

        Route::get('/transcript','TranscriptController@create')->name('transcript');

    });

    Route::group(['namespace' => 'Universities'], function() {
        Route::resource('/universities', 'UniversitiesController');
        Route::resource('/universities/{university}/departments', 'DepartmentsController');
    });

    Route::group(['namespace' => 'Noticeboard'], function() {
        Route::get('/noticeboard','NoticeBoardController@show')->name('noticeboard');
        Route::resource('/announcements', 'AnnouncementController');
    });
    
    Route::group(['namespace' => 'Issue'], function() {
        Route::get('/issue-show/{issue}','CommentsController@show')->name('issue-show');
        Route::get('/store-comment','CommentsController@store')->name('store-comment');
        Route::get('/delete-comment','CommentsController@destroy')->name('delete-comment');

        Route::resource('/issues', 'IssueController');
    });

    Route::group(['namespace' => 'Curriculum'], function() {
        Route::get('/curriculum', 'UniversitiesController@index')->name('curriculum.universities');
        Route::get('/curriculum/{university}', 'DepartmentsController@index')->name('curriculum.departments');
        Route::resource('/curriculum/{university}/{department}/subjects', 'SubjectsController');
    });

    Route::group(['namespace' => 'Course'], function() {   
        Route::get('courses/{course}/list', 'AttendanceController@list')->name('attendance.create');        
        Route::get('courses/{course}/attendance', 'AttendanceController@print')->name('course.attendance.print');
        Route::get('courses/{course}/scores-sheet', 'ScoreSheetController@print')->name('course.scoresSheet.print');
        Route::post('courses/{course}/add-student', 'AttendanceController@addStudent')->name('attendance.student.add');
        Route::delete('courses/{course}/remove-student', 'AttendanceController@removeStudent')->name('attendance.student.remove'); 
        
        Route::post('courses/{course}/scores', 'ScoresController')->name('scores.store');

        Route::post('courses/{course}/times','CourseTimeController@store')->name('course.time.store');        
        Route::get('courses/{course}/times/{courseTime}/edit', 'CourseTimeController@edit')->name('course.time.edit');
        Route::post('courses/{course}/times/{courseTime}', 'CourseTimeController@update')->name('course.time.update');
        Route::delete('courses/{course}/times/{courseTime}', 'CourseTimeController@delete')->name('course.time.destroy');

        Route::resource('courses', 'CourseController');
    });

    Route::group(['namespace' => 'Teachers'], function(){
        Route::resource('/teachers', 'TeachersController');
    });

    Route::post('/cityupdate', 'HomeController@updateData');
    Route::post('/universityupdate', 'HomeController@updateData');
    Route::get('/download/{file}','SystemDownloadController@download')->name('noticeboards.download');
    Route::get('/deletefile/{file}','FilesDeleteController@deleteFiles')->name('deletefile');

    Route::get('/activity/{university_id?}/{startdate?}/{enddate?}','ActivityController@index')->name('activity');

    Route::group(['namespace' => 'Reports'], function(){
        //students report
        Route::get('report/student' , 'StudentsReportController@index')->name('report.student');
        Route::post('report/student/create' , 'StudentsReportController@create')->name('report.student.create');

        //teacher report
        Route::get('report/teacher' , 'TeachersReportController@index')->name('report.teacher');
        Route::post('report/teacher/create' , 'TeachersReportController@create')->name('report.teacher.create');
    });

    
    //attachments link
    Route::get('getAttachment/{file_name}', function($filename){
        $path = storage_path('app').'/attachments/'.$filename;
        $image = \File::get($path);
        $mime = \File::mimeType($path);
        return \Response::make($image, 200)->header('Content-Type', $mime);
    });

    Route::get('makeNotificationAsRead', function () {    
        auth()->user()->unreadNotifications->markAsRead();
    });

    Route::get('/locale/{locale}', function ($locale) {
        
        \Session::put('locale' , $locale);

        return redirect()->back();
    })->name('locale');
});
 