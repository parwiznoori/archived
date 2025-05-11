<?php

namespace App\Http\Controllers\Curriculum;

use App\DataTables\UniversitiesDataTable;
use App\Http\Controllers\Controller;
use Maklad\Permission\Models\Permission;
use Maklad\Permission\Models\Role;

class UniversitiesController extends Controller
{
    public function __construct()
    {        
        $this->middleware('permission:view-curriculum', ['only' => ['index', 'show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UniversitiesDataTable $dataTable)
    {        
        return $dataTable->render('curriculum.universities', [
            'title' => trans('general.universities'),
            'description' => trans('general.universities_list'),
        ]);
    }
}
