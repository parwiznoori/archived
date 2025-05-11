<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use App\Models\StudentStatus;
use App\Models\University;
use DB;
use Excel;
use Illuminate\Http\Request;

class SearchStudentController extends Controller
{

    public function index()
    {   
        
        return view('search_student.index', [
            'title' => trans('general.search'),
            'description' => trans('general.student'),
        ]);
    }


    public function search(Request $request)
    { 
        $student=Student::where('form_no',$request->student_id)->first();
        // dd($student);
        // echo "search method : $request->student_id";
        return view('search_student.show', [
            'title' => trans('general.students'),
            'description' => trans('general.edit_student'),
            'student' => $student,
            'universities' => University::pluck('name', 'id'),
            'statuses' => StudentStatus::whereIn('id', [1, 2])->pluck('title', 'id'),
            'grades' => Grade::pluck('name', 'id'),
        ]);
      
        // return Excel::download(new StudentExports() , 'students.xlsx');
    }
    
}
