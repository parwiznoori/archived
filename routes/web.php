<?php

use App\Http\Controllers\Api\DepartmentsController;

Auth::routes();

Route::get('/', function () {
        return redirect('/login');
    }
);
// Route::get('/test', 'TestController@index');
// Route::Post('/test/store','TestController@store')->name('test.store');



Route::group(['middleware' => 'auth'], function() {
    Route::get('/php_info', 'TestController@php_info');
    // Route::get('/','Noticeboard\NoticeBoardController@show')->name('noticeboard');
    Route::get('develope/update_university_in_courses','DeveloperController@update_university_in_courses');
    Route::get('develope/department/type','DeveloperController@department_type');
    Route::get('develope/migrate_deprtment_deleted','DeveloperController@migrate_deprtment_deleted');

    Route::get('develope/department/faculty','DeveloperController@department_faculty');
    Route::get('develope/migration_create_department_faculty','DeveloperController@migration_create_department_faculty');
    Route::post('develope/migration_store_department_faculty','DeveloperController@migration_store_department_faculty')->name('develope.migrate.migration_store_department_faculty');

    Route::get('develope/course_group/min/{min}/max/{max}','DeveloperController@courses_group_id');
    Route::get('develope/leaves/set_department/min/{min}/max/{max}','DeveloperController@leaves_set_department');

    Route::get('profile/password','ProfileController@index')->name('profile.password');
    Route::put('profile/password','ProfileController@store')->name('profile.password.store');

    Route::resource('systemVariable', 'SystemVariableController');



//     Route::impersonate();
    Route::group(['namespace' => 'Api'], function() {
        Route::get('api/students', "StudentsController")->name('api.students');// Filter is on Model
        Route::get('api/studentsWithDepartment/{department_id?}', "StudentsController@studentsWithDepartmentId")->name('api.studentsWithDepartment');// Filter is on Model
        Route::post('api/studentsWithDepartmentAndUniversity', "StudentsController@studentsWithDepartmentAndUniversity")->name('api.studentsWithDepartmentAndUniversity');// Filter is on Model
        Route::get('api/provinces', "ProvinceController")->name('api.provinces');
        Route::get('api/subjects/{department?}', "SubjectsController")->name('api.subjects');// Filter is on Model
        Route::get('api/groups/{department?}', "GroupsController")->name('api.groups');// Filter is on Model
        Route::get('api/teachers/{university?}', "TeachersController")->name('api.teachers');// Filter is on Model
        Route::get('api/departments/{faculties?}', "DepartmentsController")->name('api.departments');
        Route::get('api/departmentArchive/{university?}', "DepartmentArchiveController")->name('api.departmentArchive');
        Route::get('api/grades', "GradesController")->name('api.grades');
        Route::get('api/faculties/{univetsity?}', "FacultiesController")->name('api.faculties');

        Route::get('api/departmentByFacultyArchive/{faculties?}', "DepartmentByFacultyArchiveController")->name('api.departmentByFacultyArchive');
    });

    Route::get('home/{kankor_year?}', 'HomeController@index')->name('home');

    Route::get('/support', function () {
        return view('support', [
            'title' => trans('general.support')
        ]);
    })->name('support');

    Route::group(['namespace' => 'Users'], function() {
        Route::resource('/users', 'UsersController');
        Route::get('users/{id}/edit-status','UsersController@editStatus')->name('users.editStatus');
        Route::post('users/{id}/update-status','UsersController@updateStatus')->name('users.updateStatus');

        Route::get('users/recover/{id}','UsersController@recover')->name('users.recover');
        Route::resource('/roles', 'RolesController');
        Route::get('roles/recover/{id}','RolesController@recover')->name('roles.recover');
        Route::resource('/permissions', 'PermissionController');
        Route::get('permissions/recover/{id}','PermissionController@recover')->name('permissions.recover');
        Route::get('users/{user}/auth-logs', 'UserAuthLogsController@index')->name('user.auth.log');
        Route::get('activities/{object}/list', 'logsActivityController@index')->name('activities.list');
        Route::get('activities/{object}/show/{log}', 'logsActivityController@show')->name('activities.show');
        Route::get('all-logs', 'logsActivityController@allLogs')->name('allLogs');
        Route::get('all-logs/show/{id}', 'logsActivityController@showLog')->name('allLogs.showLog');
    });


    /////route for courses by gorups
    Route::group(['namespace' => 'Groups','as' => 'groups.'], function() {
        Route::get('groups', 'GroupsController@index');
        Route::resource('groups/{group}/courses', 'CourseController');

    });

    Route::group(['namespace' => 'Students'], function() {
        Route::get('student/groups/create-all','Groups\CreateGroupsController@index')->name('student.groups.all.create');
        Route::post('student/groups/create-all','Groups\CreateGroupsController@store')->name('student.groups.all.store');

        Route::get('/students/groups/merge','Groups\GroupsController@mergeGroupsForm')->name('groups.merge');
        Route::post('/students/groups/merge-groups-store','Groups\GroupsController@mergeGroupsStore')->name('groups.merge-groups-store');
        Route::resource('/students/groups', 'Groups\GroupsController');

        Route::get('/students/groups/recover/{id}','Groups\GroupsController@recover')->name('groups.recover');
        Route::get('/students/groups/{group}/list', 'Groups\GroupListController@index')->name('groups.list');
        Route::post('/students/groups/{group}/list', 'Groups\GroupListController@addStudent')->name('groups.student.add');
        Route::delete('/students/groups/{group}/list', 'Groups\GroupListController@removeStudent')->name('groups.student.remove');
        Route::get('/students/groups/{group}/courses-list', 'Groups\GroupListController@courses_list')->name('groups.courses_list');
        Route::get('student/groups/groups-automation','Groups\GroupsAutomationController@index')->name('student.groups.automation');
        Route::post('student/groups/groups-automation/generate','Groups\GroupsAutomationController@store')->name('student.groups.automation.generate');

        Route::get('/students/edit_kankor_results', 'StudentsController@add_students_to_edit_kankor_rsults')->name('students.add_students_to_edit_kankor_rsults');
        Route::post('/students/edit_kankor_results', 'StudentsController@update_kankor_results')->name('students.update_kankor_results');

        Route::resource('/students', 'StudentsController');

        Route::patch('/students/{student}/updateStatus', 'StudentsController@updateStatus')->name('students.updateStatus');
        Route::get('/students/{student}/allDetails', 'StudentAllDetailsController@index')->name('students.allDetails');
        Route::get('/students/{student}/card', 'StudentCardController@index')->name('students.card');
        Route::get('/students/{student}/show-card', 'StudentCardController@show_card')->name('students.show-card');
        Route::get('/students-card/print', 'StudentCardController@printAllStudentsForm')->name('students.card.form');
        Route::post('/students-card/print', 'StudentCardController@printAllStudentsCard')->name('print.students.card');
        Route::get('/students/{student}/showgroup', 'StudentsController@showgroup')->name('students.showgroup');
        Route::get('/students/{student}/photo', 'PhotoController@index')->name('students.photo');
        Route::put('/students/{student}/photo', 'PhotoController@store')->name('students.photo.update');

        Route::get('{student}/downloads', 'StudentDownloadController@index')->name('students.downloads');
        Route::get('{student}/downloads/{file}', 'StudentDownloadController@show')->name('students.downloads.download');

        Route::get('/transfers/report', 'TransfersController@report')->name('transfers.report');
        Route::Post('/transfers/report', 'TransfersController@report_search')->name('transfers.report.search');
        Route::get('/transfers/exception', 'TransfersController@create_exception')->name('transfers.create_exception');
        Route::Post('/transfers/exception', 'TransfersController@store_exception')->name('transfers.store_exception');
        Route::resource('/transfers', 'TransfersController');
        Route::get('/transfers/{transfer}/approved', 'TransfersController@approved')->name('transfers.approved');
        Route::get('/transfers/{transfer}/download-pdf', 'TransfersController@download_pdf')->name('transfers.download-pdf');
        Route::get('transfers/recover/{id}','TransfersController@recover')->name('transfers.recover');


        Route::get('/dropouts/{dropout}/approved', 'DropoutsController@approved')->name('dropouts.approved');
        Route::get('/dropouts/create-all' , 'CreateAllDropoutController@index')->name('dropout.create-all');
        Route::Post('/dropouts/create-all/craete' , 'CreateAllDropoutController@create')->name('dropout.create-all.create');
        Route::get('/dropouts/{dropout}/removal', 'DropoutsController@removal')->name('dropouts.removal');
        Route::post('/dropouts/{dropout}/removal', 'DropoutsController@removal_store')->name('dropouts.removal_store');
        Route::resource('/dropouts', 'DropoutsController');

        Route::resource('/leaves', 'LeavesController', ['parameters' => [
            'leaves' => 'leave'
        ]]);
        Route::get('/leaves/{leave}/approved', 'LeavesController@approved')->name('leaves.approved');
        Route::get('leaves/{leave}/end-leave' , 'EndLeaveController@index')->name('leaves.end_leave');

        //students forms
        Route::get('students/{student}/student-form' , 'StudentFormsController@index')->name('student.form');
        Route::post('students/{student}/generate-form' , 'StudentFormsController@generateForm')->name('student.generate-form');

        Route::get('semester-base/show-form', 'StudentsResultController@index')->name('students.semester-base.result');

        Route::Post('semester-base/create-results', 'StudentsResultController@create_results')->name('students.semester-base.create_results');

        Route::get('semester-base/show', 'StudentsResultController@show')->name('students.semester-base.show');

        Route::Post('semester-base/result/show-result', 'StudentsResultController@showResult')->name('students.semester-base.result.show.result');
        Route::Post('semester-base/result/move-to-next-semester', 'StudentsResultController@movedToNextSemester')->name('students.semester-base.result.moved-to-next-semester');



        //for Deploma and expire date controller
        Route::get('/students/{student}/diplomaphoto', 'DiplomaphotoController@index')->name('students.diplomaphoto');
        Route::put('/students/{student}/diplomaphoto', 'DiplomaphotoController@store')->name('students.diplomaphoto.update');
        Route::get('/students/{student}/expiredate', 'ExpiredateController@index')->name('students.expiredate');
        Route::post('/students/{student}/expiredate', 'ExpiredateController@update')->name('students.expiredate.update');
        // graduated students route
        Route::get('/graduated-students/show-students-form', 'GraduatedStudentsController@show_form')->name('graduate.check.form');
        Route::post('/graduated-students/show-students-form', 'GraduatedStudentsController@showResult')->name('graduate.check.list');
        Route::post('/graduated-students/change-status', 'GraduatedStudentsController@change_status')->name('graduate.change_status');

        Route::get('/graduate-book', 'GraduateBookController@index')->name('graduate-book.index');
        Route::get('/graduate-book/create-form', 'GraduateBookController@createForm')->name('graduate-book.create-form');
        Route::post('/graduate-book/pdf', 'GraduateBookController@pdf')->name('graduate-book.pdf');
        Route::get('/graduate-book/{id}/download', 'GraduateBookController@download')->name('graduate-book.download');
        Route::get('/graduate-book/{id}/show', 'GraduateBookController@show')->name('graduate-book.show');


        Route::get('/students/{student}/manual-graduate', 'GraduatedStudentsController@manualGraduate')->name('students.manualGraduate');
        Route::post('/students/graduate/manual-graduate', 'GraduatedStudentsController@manualGraduateStore')->name('students.manualGraduateStore');

        Route::get('graduated-students/{student_id}/transcript','TranscriptController@create')->name('student.transcript');
        Route::get('graduated-students/{student_id}/transcript-dr','TranscriptController@transcriptDari')->name('student.transcript-dr');
        Route::get('graduated-students/{student_id}/transcript-ps','TranscriptController@transptPashto')->name('student.transcript-ps');

        Route::get('graduated-students/{student_id}/graduate-results','GraduatedStudentsController@graduateResults')->name('student.graduate-results');
        Route::get('graduated-students/{student_id}/diploma','DiplomaController@index')->name('student.diploma');

        Route::resource('/graduated-students', 'GraduatedStudentsController');
        Route::resource('semester-deprived-student', 'SemesterDeprivesStudentController');
        Route::get('semester-deprived-student/recover/{id}','SemesterDeprivesStudentController@recover')->name('semester-deprived-student.recover');

    });

    Route::group(['namespace' => 'Universities'], function() {
        Route::resource('/universities', 'UniversitiesController');
        Route::resource('/universities/{university}/departments', 'DepartmentsController');
        Route::get('/universities/{university}/departments/recover/{id}','DepartmentsController@recover')->name('departments.recover');
        Route::resource('/universities/{university}/faculties', 'FacultyController');
        Route::get('/universities/{university}/faculties/{faculty}/departments-list', 'FacultyController@departments_list')->name('faculties.departments_list');
        Route::resource('/departmentType', 'DepartmentTypeController');
        Route::get('/universities/{university}/users-list','UniversitiesController@users_list')->name('university.users_list');
        Route::get('/universities/{university}/activities/{year?}','UniversitiesController@activities')->name('university.activities');
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
        Route::get('/curriculum/{university}/{department}/subjects/{id}/edit-credit','SubjectsController@editCredit')->name('curriculum.editCredit');
        Route::post('/curriculum/{university}/{department}/subjects/{id}/update-credit','SubjectsController@updateCredit')->name('curriculum.updateCredit');
        Route::get('/curriculum/{university}/{department}/subjects/{subject}/courses-list', 'SubjectsController@courses_list')->name('subjects.courses_list');
    });

    Route::group(['namespace' => 'Course'], function() {
        Route::get('courses/{course}/list', 'AttendanceController@list')->name('attendance.create');
        Route::get('courses/{course}/midterm', 'AttendanceController@midterm')->name('course.midterm');
        Route::get('courses/{course}/final', 'AttendanceController@final_exam')->name('course.final');
        Route::get('courses/{course}/chance1-approved', 'AttendanceController@chance1_approved')->name('course.chance1_approved');
        Route::post('courses/{course}/final_approved_chance1_insert_scores', 'AttendanceController@final_approved_chance1_insert_scores')->name('course.final_approved_chance1_insert_scores');
        Route::post('courses/{course}/update_present_in_score_table', 'AttendanceController@update_present_in_score_table')->name('course.update_present_in_score_table');

        Route::get('courses/{course}/chance2', 'AttendanceController@chance2_exam')->name('course.chance2');
        Route::get('courses/{course}/chance2-approved', 'AttendanceController@chance2_approved')->name('course.chance2_approved');
        Route::post('courses/{course}/final_approved_chance2_insert_scores', 'AttendanceController@final_approved_chance2_insert_scores')->name('course.final_approved_chance2_insert_scores');

        Route::get('courses/{course}/chance3', 'AttendanceController@chance3_exam')->name('course.chance3');
        Route::get('courses/{course}/chance3-approved', 'AttendanceController@chance3_approved')->name('course.chance3_approved');
        Route::post('courses/{course}/final_approved_chance3_insert_scores', 'AttendanceController@final_approved_chance3_insert_scores')->name('course.final_approved_chance3_insert_scores');

        Route::get('courses/{course}/chance4', 'AttendanceController@chance4_exam')->name('course.chance4');
        Route::get('courses/{course}/chance4-approved', 'AttendanceController@chance4_approved')->name('course.chance4_approved');
        Route::post('courses/{course}/final_approved_chance4_insert_scores', 'AttendanceController@final_approved_chance4_insert_scores')->name('course.final_approved_chance4_insert_scores');

        Route::get('courses/{course}/deprived', 'AttendanceController@deprived_student')->name('course.deprived');
        Route::get('courses/{course}/absent', 'AttendanceController@absent_student')->name('course.absent');
        Route::get('courses/{course}/excuse', 'AttendanceController@excuse_exam')->name('course.excuse');
        Route::get('courses/{course}/all-chances', 'AttendanceController@all_chances_scores')->name('course.all_chances_scores');
        Route::get('courses/{course}/final-approved', 'AttendanceController@final_approved_show')->name('course.final_approved_show');
        Route::post('courses/{course}/final_approved_insert_scores', 'AttendanceController@final_approved_insert_scores')->name('course.final_approved_insert_scores');
        Route::get('courses/{course}/attendance', 'AttendanceController@print')->name('course.attendance.print');
        Route::get('courses/{course}/scores-sheet', 'ScoreSheetController@print')->name('course.scoresSheet.print');

        Route::get('courses/{course}/scores-sheet/chance1', 'ScoreSheetController@print_chance1')->name('course.scoresSheet.print_chance1');
        Route::get('courses/{course}/scores-sheet/chance2', 'ScoreSheetController@print_chance2')->name('course.scoresSheet.print_chance2');
        Route::get('courses/{course}/scores-sheet/chance3', 'ScoreSheetController@print_chance3')->name('course.scoresSheet.print_chance3');
        Route::get('courses/{course}/scores-sheet/chance4', 'ScoreSheetController@print_chance4')->name('course.scoresSheet.print_chance4');

        Route::get('courses/{course}/export-excel/chance1', 'ScoreSheetController@export_excel_chanc1')->name('course.scoresSheet.export-excel-chance1');
        // Route::get('courses/{course}/import-excel/chance1', 'AttendanceController@import_chance1_from_excel')->name('import.import_chance1_from_excel');
        // Route::post('courses/{course}/import-excel/chance1', 'AttendanceController@score_chance1_from_excel')->name('import.score_chance1_from_excel');

        Route::post('courses/{course}/add-student', 'AttendanceController@addStudent')->name('attendance.student.add');
        Route::delete('courses/{course}/remove-student', 'AttendanceController@removeStudent')->name('attendance.student.remove');
        Route::delete('courses/{course}/remove-score', 'AttendanceController@removeScore')->name('attendance.score.remove');
        Route::post('courses/{course}/scores', 'ScoresController')->name('scores.store');

        Route::post('courses/{course}/times','CourseTimeController@store')->name('course.time.store');
        Route::post('courses/{course}/groups','CourseController@update_groups')->name('course.update_groups');
        Route::get('courses/{course}/times/{courseTime}/edit', 'CourseTimeController@edit')->name('course.time.edit');
        Route::post('courses/{course}/times/{courseTime}', 'CourseTimeController@update')->name('course.time.update');
        Route::delete('courses/{course}/times/{courseTime}', 'CourseTimeController@delete')->name('course.time.destroy');

        Route::resource('lesson_weeks', 'LessonWeekController');

        Route::resource('courses', 'CourseController');
        Route::get('courses/recover/{id}','CourseController@recover')->name('courses.recover');
        Route::get('courses/backToNormal/{id}','CourseController@backToNormal')->name('courses.backToNormal');
        Route::get('courses/removeTeacherApproved/{id}','CourseController@removeTeacherApproved')->name('courses.removeTeacherApproved');

        Route::resource('monographs', 'MonographController');
    });

    
    //archive rout

    Route::get('departments/{facultyId}', [DepartmentsController::class, 'getDepartments']);

    Route::group(['namespace' => 'Archive'], function() {

        Route::resource('archive', 'ArchiveController');
        Route::resource('archiveimage', 'ArchiveimageController');
        Route::resource('archivedata', 'ArchivedataController');
        Route::get('/archiveLoadPage/{id}','ArchivedataController@loadPage');

       
       

            // Selection page (no ID needed)
        Route::get('/update-name', 'ArchivedataController@selectForNameUpdate')
            ->name('archivedata.select-for-update')
            ->middleware('can:update-name');

        // Edit page (requires ID)
        Route::get('/archivedata/{archivedata}/edit-name', 'ArchivedataController@showEditNameForm')
            ->name('archivedata.edit-name');
            

        Route::put('/archivedata/{archivedata}/update-name', 'ArchivedataController@updateName')
        ->name('archivedata.update-name');


        Route::get('/archive/view/{archiveId}', 'ArchiveController@viewCsv')->name('archive.view');
        Route::post('/import', 'ArchiveController@import')->name('archivedata.import');
        Route::get('download-archive-template', 'ArchiveController@downloadTemplate')->name('downloadTemplate');

        Route::get('/import/undo', 'ArchiveController@undoLastUpload')->name('import.undoLastUpload');


        Route::get('/archiveBookDataEntry/{id}','ArchivedataController@archiveBookDataEntry')->name('archiveBookDataEntry');

        Route::get('/archiveBookDataEntryPage/{pageId}/{id}/{step}','ArchivedataController@archiveBookDataEntryPage')->name('archiveBookDataEntryPage');

        Route::get('/archiveBookDataEntryPageReject/{pageId}/{id}/{step}','ArchivedataController@archiveBookDataEntryPageReject')->name('archiveBookDataEntryPageReject');
        Route::get('/archiveBookDataEntryPageData/{pageId}/{id}/{column}','ArchivedataController@archiveBookDataEntryPageData')->name('archiveBookDataEntryPageData');
        Route::post('/archiveBookDataEntryApprove','ArchivedataController@archiveBookDataEntryApprove')->name('archiveBookDataEntryApprove');
        Route::get('/archiveBookDataEntryApprove','ArchivedataController@backRedirect')->name('backRedirect');

        Route::get('archivedatas','ArchivesearchController@index')->name('archivedatas');
        Route::post('archivesearch','ArchivesearchController@search')->name('archivesearch');
        Route::get('/archiveimage/{id}/{total_students}','ArchiveimageController@setPageTotal')->name('setPageTotal');
        Route::get('archiveqc','ArchiveqcController@archvieqc')->name('archiveqc');
        Route::get('archiveqcheck/{id}','ArchiveqcController@archiveqcheck')->name('archiveqcheck');

        Route::get('archiveqcheckdata/{archive_id}/{image_archive_id}','ArchiveqcController@archiveqcheckdata')->name('archiveqcheckdata');
        Route::post('archiveBookDataEntrySearch','ArchivedataController@archiveBookDataEntrySearch')->name('archiveBookDataEntrySearch');
        Route::post('archiveqcheckdataSetStatus','ArchiveqcController@archiveqcheckdataSetStatus')->name('archiveqcheckdataSetStatus');
        Route::post('archiveqcheckImageSetStatus','ArchiveqcController@archiveqcheckImageSetStatus')->name('archiveqcheckImageSetStatus');
        Route::post('/convert-pdf-to-jpg', 'PDFToJPGController@convert')-> name('convert');

        Route::resource('archiveuser', 'ArchiveuserController');

        Route::resource('archiverole', 'ArchiveRoleController');
        Route::get('/archiverole/{name}/{id}', "ArchiveRoleController@show")->name('archiverole');



        Route::get('archiveBookRoleLoad/{university_id?}/{role_id?}', "ArchiveRoleController@archiveBookRoleLoad")->name('api.archiveBookRoleLoad');
        Route::get('archiveUserRoleLoad/{role_id?}', "ArchiveRoleController@archiveUserRoleLoad")->name('api.archiveUserRoleLoad');


        Route::post('/reset-qc-user/{archive}', 'ArchiveController@resetQcUser')-> name('archive.reset-qc-user');
        Route::post('/reset-de-user/{archive}', 'ArchiveController@resetDeUser')-> name('archive.reset-de-user');

        Route::get('archive_report', 'ArchivereportController@index')->name('archive_report');
        Route::get('archive_report2', 'ArchivereportController@report2')->name('archive_report2');
        Route::post('reportresult', 'ArchivereportController@reportresult')->name('reportresult');
        Route::get('print-archivedoc/{id}', 'ArchivedocController@index')->name('print-archivedoc');
        Route::get('print-archivedocf/{id}', 'ArchivedocController@fdoc')->name('print-archivedocf');
        Route::get('print-archivedestalam/{id}', 'ArchivedocController@archivedestalam')->name('print-archivedestalam');
        Route::get('print-archivedestalam2/{id}', 'ArchivedocController@archivedestalam2')->name('print-archivedestalam2');

        Route::get('archivedata_detials/{id}', 'ArchivedataController@show')->name('archivedata_detials');



        Route::get('archive_doc_type/{archivedata}', 'ArchiveDocTypeController@show')->name('archive_doc_type');
        Route::post('archive_doc_type2', 'ArchiveDocTypeController@store')->name('archive_doc_type2');
        Route::get('archive_doc_type3/{id}', 'ArchiveDocTypeController@edit')->name('archive_doc_type3');
        Route::post('archive_doc_type3/{id}', 'ArchiveDocTypeController@update')->name('archive_doc_type3');
        Route::delete('archive_doc_type3/{id}', 'ArchiveDocTypeController@destroy')->name('archive_doc_type3.destroy');


        Route::resource('archive_doc_form', 'ArchiveDocFormController');

        Route::get('archive_form_print/{archivedataid}', 'ArchiveDocFormPrintController@show')->name('archive_form_print');
        Route::post('archive_form_print2/{archivedataid}', 'ArchiveDocFormPrintController@store')->name('archive_form_print2');
        Route::delete('archive_form_print/{id}', 'ArchiveDocFormPrintController@destroy')->name('archive_form_print.destroy');

        Route::get('print-archivedocform/{archivedataid}', 'ArchiveDocFormPrintController@printform')->name('print-archivedocform');

        Route::get('archive_monograph/{id}', 'ArchiveMonographController@show')->name('archive_monograph');
        Route::post('archive_monograph_update/{id}', 'ArchiveMonographController@insert')->name('archive_monograph_update');

        Route::get('archive_baqidari/{id}', 'ArchiveBaqidariController@show')->name('archive_baqidari');
        Route::post('archive_baqidari_update/{id}', 'ArchiveBaqidariController@insert')->name('archive_baqidari_update');


    });


    Route::get('/test',function(){
        return public_path().'\archivefiles\\';
    });



    Route::group(['namespace' => 'Teachers'], function(){
        Route::resource('/teachers', 'TeachersController');
        Route::get('teachers/{id}/edit-status','TeachersController@editStatus')->name('teachers.editStatus');
        Route::post('teachers/{id}/update-status','TeachersController@updateStatus')->name('teachers.updateStatus');
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
        Route::post('report/student/all' , 'StudentsReportController@all')->name('report.student.all');

        // Route::get('report/student/report' , 'StudentsReportController@report')->name('report.student.report');

        //teacher report
        Route::get('report/teacher' , 'TeachersReportController@index')->name('report.teacher');
        Route::post('report/teacher/create' , 'TeachersReportController@create')->name('report.teacher.create');
    });

    Route::group(['namespace' => 'Statistics'], function(){
        Route::get('statistics/student' , 'StudentsStatisticsController@index')->name('statistics.student');
        Route::post('statistics/student/show' , 'StudentsStatisticsController@show')->name('statistics.student.show');
        Route::post('statistics/student/show_by_province' , 'StudentsStatisticsController@show_by_province')->name('statistics.student.by_province');
        Route::post('statistics/student/show_by_university' , 'StudentsStatisticsController@show_by_university')->name('statistics.student.by_university');
        Route::post('statistics/student/show_by_department' , 'StudentsStatisticsController@show_by_department')->name('statistics.student.by_department');
        Route::post('statistics/student/show_by_gender' , 'StudentsStatisticsController@show_by_gender')->name('statistics.student.by_gender');
        Route::post('statistics/student/show_by_grade' , 'StudentsStatisticsController@show_by_grade')->name('statistics.student.by_grade');
    });

    // Route::group(['namespace' => 'KankorDataMigration'], function(){
    //     // Route::get('kankor_results/university_id' , 'KankorResultsController@show_university')->name('kankor_results.show_university');
    //     // Route::post('kankor_results/university_id/store' , 'KankorResultsController@store_university')->name('kankor_results.store_university');

    //     // Route::get('kankor_results/show-enrollment-type-form' , 'KankorResultsController@show_enrollment_type_form')->name('kankor_results.show_enrollment_type_form');
    //     // Route::post('kankor_results/store-enrollment-type' , 'KankorResultsController@store_enrollment_type')->name('kankor_results.store_enrollment_type');

    //     Route::get('kankor_results/department_id' , 'KankorResultsController@show_department')->name('kankor_results.show_department');
    //     Route::post('kankor_results/department_id/store' , 'KankorResultsController@store_department')->name('kankor_results.store_department');

    //     // Route::get('kankor_results/university_id_by_kankor_results' , 'KankorResultsController@show_university_by_kankor_results')->name('kankor_results.show_university_by_kankor_results');
    //     // Route::post('kankor_results/university_id_by_kankor_results/store' , 'KankorResultsController@store_university_by_kankor_results')->name('kankor_results.store_university_by_kankor_results');

    //     // Route::get('kankor_results/koochi/university_id_by_kankor_results' , 'KankorResultsController@show_university_by_kankor_results_koochi')->name('kankor_results.show_university_by_kankor_results_koochi');
    //     // Route::post('kankor_results/koochi/university_id_by_kankor_results/store' , 'KankorResultsController@store_university_by_kankor_results_koochi')->name('kankor_results.store_university_by_kankor_results_koochi');
    // });


    Route::group(['namespace' => 'KankorDataMigration'], function(){

    Route::get('kankor_results/department_id', 'KankorResultsController@show_department')->name('kankor_results.show_department');
    Route::post('kankor_results/department_id/store', 'KankorResultsController@store_department')->name('kankor_results.store_department');
     });

    //attachments link
    Route::get('getAttachment/{file_name}', function($filename){
        $path = storage_path('app').'/attachments/'.$filename;
        $image = \File::get($path);
        $mime = \File::mimeType($path);
        return \Response::make($image, 200)->header('Content-Type', $mime);
    });

    Route::get('makeNotificationAsRead/{id}', function ($id) {
        $notification=auth()->user()->notifications->where('id',$id)->first();
        $notification->markAsRead();
    });
    Route::get("migration" , "MigrationController@index");
    Route::post("migration/process" , "MigrationController@process")->name('migration.process');

    Route::get('/locale/{locale}', function ($locale) {

        \Session::put('locale' , $locale);

        return redirect()->back();
    })->name('locale');

//system
    Route::group(['namespace' => 'System'], function() {
        Route::resource('/shiftType', 'ShiftTypeController');
        Route::get('/login_report', 'LoginReportController@getLog')->name('login_report.index');
        Route::get('/login_report/showReport', 'LoginReportController@showReport')->name('login_report.showReport');
        Route::resource('/province', 'ProvinceController');
        Route::resource('/day', 'DayController');
        Route::resource('/role-type', 'RoleTypeController');
        Route::resource('/teacheracademicrank', 'TeacherAcademicRankController');
        Route::resource('/studentstatus', 'StudentStatusController');
        Route::resource('/grades', 'GradesController');



    });

    Route::group(['namespace' => 'Search'], function() {
        Route::get('/searchStudent' , 'SearchStudentController@index')->name('searchStudent.index');
        Route::post('/searchStudent' , 'SearchStudentController@search')->name('searchStudent.search');

    });


});

Route::get('/welcome', function () {

    dispatch(function() {
        sleep(5);
        logger('job done!');
    });

    return view('welcome');
});
 