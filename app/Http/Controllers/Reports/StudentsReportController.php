<?php
namespace App\Http\Controllers\Reports;

use App\Exports\AllStudentExports;
use App\Exports\DropoutStudentExports;
use App\Exports\GraduateStudentExports;
use App\Exports\LeaveStudentExports;
use App\Exports\RegisteredStudentExports;
use App\Exports\TransferStudentExports;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Grade;
use App\Models\Province;
use App\Models\Student;
use App\Models\Transfert;
use App\Models\University;
use DB;
use Excel;
use Illuminate\Http\Request;

class StudentsReportController extends Controller
{
    public function index()
    {   
        $studentsArray=array();
        $kankorYear = Student::max('kankor_year');
        if( auth()->user()->allUniversities() ) {                        
            $universities =  University::pluck('name', 'id');
            $departments = [];
        }
        else 
        {            
            $universities =  University::where('id',auth()->user()->university_id)->pluck('name', 'id');
            $countDepartments= auth()->user()->departments->count();
            if($countDepartments > 0)
            {
                $departments = Department::pluck('name', 'id');
            }
            else{
                $departments = Department::pluck('name', 'id')->prepend(trans('general.please_select_one'),'');
            }
           
        } 
        // $statuses_options = StudentStatus::pluck('title', 'id');
        // $statuses = $statuses_options->merge([6 => 'تبدیل']);
        $statuses = [
            1 => "کامیاب کانکور",
            2 => "شامل پوهنتون",
            3 => "منفک",
            4 => "تاجیل",
            5 => "فارغ التحصیل",
            6 => "تبدیل"
        ];

        return view('reports.students.index', [
            'title' => trans('general.report'),
            'description' => trans('general.student_report'),
            'universities' => $universities,
            'provinces' => Province::pluck('name','id'),
            'kankor_years' =>  Student::select('kankor_year')->distinct()->orderBy('kankor_year', 'desc')->pluck('kankor_year','kankor_year'),
            'kankorYear' => $kankorYear,
            'statuses' => $statuses,
            'grades' => Grade::pluck('name', 'id'),
            'department' =>  $departments,
            'studentsArray' => $studentsArray,
            'has_student_statistics' => 0
        ]);
    }

    public function create(Request $request){ 
        // dd($request);
        $type = $request->status;
        switch($type)
        {
            case 1 :
                return Excel::download(new AllStudentExports, 'all-students.xlsx');
                break;
            case 2 :
                return Excel::download(new RegisteredStudentExports, 'registered-students.xlsx');
                break;
            case 3 :
                return Excel::download(new DropoutStudentExports, 'dropout-students.xlsx');
                break;
            case 4 :
                return Excel::download(new LeaveStudentExports, 'leave-students.xlsx');
                break;
            case 5 :
                return Excel::download(new GraduateStudentExports, 'graduate-students.xlsx');
                break;
            case 6 :
                return Excel::download(new TransferStudentExports, 'transfer-students.xlsx');
                break;
        }
        
    }
}
