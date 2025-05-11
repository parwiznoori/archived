<?php

namespace App\Http\Controllers\Users;
use App\DataTables\AllLogsActivityDataTable;
use App\DataTables\LogsActivityTable;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class logsActivityController extends Controller
{
    public function index(LogsActivityTable $dataTable){
        
        return $dataTable->render('logs_activities.index',[
            'title' => trans('general.activities'),
            'description' => trans('general.activities_list'),
        ]);
    }

    public function show($object , $log){
        return view('logs_activities.show',[
            'title' => trans('general.activities'),
            'description' => trans('general.activity_show'),
            'log' => $log,
        ]);
    }

    public function allLogs(AllLogsActivityDataTable $dataTable){
        if( (auth()->user()->hasRole('system-developer')) )
        {
            return $dataTable->render('logs_activities.all-logs',[
                'title' => trans('general.activities'),
                'description' => trans('general.activities_list'),
            ]);
        }
    }

    public function showLog($id){
        if( (auth()->user()->hasRole('system-developer')) )
        {
            $log = Activity::find($id);
            return view('logs_activities.show',[
                'title' => trans('general.activities'),
                'description' => trans('general.activity_show'),
                'log' => $log,
            ]);
        }
    }
       
}
