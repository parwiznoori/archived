<?php

namespace App\Http\Controllers\Users;

use App\DataTables\UserAuthLogsDataTable;
use App\Http\Controllers\Controller;

class UserAuthLogsController extends Controller 
{
    
    public function index(UserAuthLogsDataTable $dataTable , $user){

        return $dataTable->render('users.logs.index', [
            'title' => trans('general.users'),
            'description' => trans('general.user_logs'),
        ]);
    }
}
