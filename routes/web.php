<?php

use App\Http\Controllers\Api\DepartmentsController;

Auth::routes();

Route::get('/', function () {
        return redirect('/login');
    }
);



Route::group(['middleware' => 'auth'], function() {
    Route::get('/php_info', 'TestController@php_info');
    Route::get('develope/department/type','DeveloperController@department_type');
    Route::get('develope/migrate_deprtment_deleted','DeveloperController@migrate_deprtment_deleted');
    Route::get('develope/department/faculty','DeveloperController@department_faculty');
    Route::get('develope/migration_create_department_faculty','DeveloperController@migration_create_department_faculty');
    Route::post('develope/migration_store_department_faculty','DeveloperController@migration_store_department_faculty')->name('develope.migrate.migration_store_department_faculty');
    Route::get('develope/leaves/set_department/min/{min}/max/{max}','DeveloperController@leaves_set_department');
    Route::get('profile/password','ProfileController@index')->name('profile.password');
    Route::put('profile/password','ProfileController@store')->name('profile.password.store');
    Route::resource('systemVariable', 'SystemVariableController');



//     Route::impersonate();
    Route::group(['namespace' => 'Api'], function() {
        Route::get('api/provinces', "ProvinceController")->name('api.provinces');
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

   
   
    
    //archive route

    Route::get('departments/{facultyId}', [DepartmentsController::class, 'getDepartments']);

    Route::group(['namespace' => 'Archive'], function() {

        Route::resource('archive', 'ArchiveController');
        Route::resource('archiveimage', 'ArchiveimageController');
        Route::resource('archivedata', 'ArchivedataController');
        Route::get('/archiveLoadPage/{id}','ArchivedataController@loadPage');
            // Selection page (no ID needed)
        Route::get('/update-name', 'ArchivedataController@selectForNameUpdate')
            ->name('archivedata.select-for-update');
           
        // Edit page (requires ID)
        Route::get('/archivedata/{archivedata}/edit-name', 'ArchivedataController@showEditNameForm')
            ->name('archivedata.edit-name');
            
        Route::put('/archivedata/{archivedata}/update-name', 'ArchivedataController@updateName')
        ->name('archivedata.update-name');

        Route::get('/archive/view/{archiveId}', 'ArchiveController@viewCsv')->name('archive.view');
        Route::post('/import', 'ArchiveController@import')->name('archivedata.import');

        Route::get('download-archive-template', 'ArchiveController@downloadTemplate')->name('downloadTemplate');

       
         Route::get('/import/undoLastUpload/{archiveId}', 'ArchiveController@undoLastUpload')
        ->name('import.undoLastUpload');
         Route::get('/import/undoBookUpload/{archiveId}','ArchiveController@undoBookUpload')
         ->name('import.undoBookUpload');



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

        Route::get('archive_report3', 'ArchivereportDocController@report3')->name('archive_report3');
        Route::post('reportresult3', 'ArchivereportDocController@reportresult3')->name('reportresult3');
        
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
        Route::get('archive_zamayem/{id}', 'ArchiveZamayemController@show')->name('archive_zamayem');
        Route::post('archive_zamayem_update/{id}', 'ArchiveZamayemController@insert')->name('archive_zamayem_update');
        Route::delete('archive_zamayem/{id}', 'ArchiveZamayemController@destroy')->name('archive_zamayem.destroy');

    });


    Route::get('/test',function(){
        return public_path().'\archivefiles\\';
    });



 

    Route::post('/cityupdate', 'HomeController@updateData');
    Route::post('/universityupdate', 'HomeController@updateData');
    Route::get('/download/{file}','SystemDownloadController@download')->name('noticeboards.download');
    Route::get('/deletefile/{file}','FilesDeleteController@deleteFiles')->name('deletefile');

    Route::get('/activity/{university_id?}/{startdate?}/{enddate?}','ActivityController@index')->name('activity');

  

   



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
        Route::resource('/grades', 'GradesController');



    });




});

Route::get('/welcome', function () {

    dispatch(function() {
        sleep(5);
        logger('job done!');
    });

    return view('welcome');
});
 