<?php

namespace App\Http\Controllers\Groups;

use App\DataTables\GroupsDataTable;
use App\Http\Controllers\Controller;

class GroupsController extends Controller
{
    public function __construct()
    {        
        $this->middleware('permission:view-group', ['only' => ['index', 'show']]);        
        
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(GroupsDataTable $dataTable)
    {        
        return $dataTable->render('groups.index', [
            'title' => trans('general.groups'),
            'description' => trans('general.groups_list')
        ]);
    }
}