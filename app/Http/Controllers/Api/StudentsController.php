<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentsController extends Controller
{
    public function __invoke(Request $request)
    {
       $students =  Student::select('id', \DB::raw('CONCAT(form_no, "-", name, "-", last_name,"-",father_name,"-",kankor_year ) as text'));

        if ($request->q != '') {
            $students->where('form_no', 'like', '%'.$request->q.'%')
                ->orWhere('name', 'like', '%'.$request->q.'%')
                ->orWhere('father_name', 'like', '%'.$request->q.'%')
                ->take(5);
        }  
        return $students->get();
    }

    public function studentsWithDepartmentAndUniversity(Request $request)
    {
        if ($request->student_id != '') {
            $student =  Student::select('students.id', 'students.form_no','students.department_id','students.university_id','universities.name as university_name','departments.name as department_name')
            ->leftJoin('universities', 'universities.id', '=', 'university_id')
            ->leftJoin('departments', 'departments.id', '=', 'department_id')
            ->where('students.id',$request->student_id)
            ->first()
            ;
        }
        $data['id']=$student->id;
        $data['form_no']=$student->form_no;
        $data['university_name']=$student->university_name;
        $data['department_name']=$student->department_name;
                
        return  $data;

    }

    public function studentsWithDepartmentId(Request $request)
    {
        // dd($request->department_id);
        $students =  Student::select('id', \DB::raw('CONCAT(form_no, " ", name, " ", last_name ,"-",father_name) as text'))->where('department_id',$request->department_id);

    //    dd(($students));

        if ($request->q != '') {
            $students->Where(function($query) use($request) 
            {  
                $query->where('form_no', 'like', '%'.$request->q.'%')
                ->orWhere('name', 'like', '%'.$request->q.'%')
                ->orWhere('father_name', 'like', '%'.$request->q.'%')
                ->take(10);
            });
        }
                
        return $students->take(10)->get();

    }
}
