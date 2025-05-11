<?php

namespace App\Http\Controllers\Reports;

use App\Exports\TeacherExports;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Province;
use App\Models\TeacherAcademicRank;
use App\Models\University;
use Excel;
use Illuminate\Http\Request;

class TeachersReportController extends Controller
{

    public function index()
    {   
        
        return view('reports.teachers.index', [
            'title' => trans('general.report'),
            'description' => trans('general.teacher_report'),
            'universities' => University::pluck('name', 'id'),
            'provinces' => Province::pluck('name','id'),
            'teacher_academic_rank' => TeacherAcademicRank::pluck('title', 'id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : []
        ]);
    }

    public function create(Request $request){ 
        return Excel::download(new TeacherExports() , 'teachers.xlsx');
    }

}
