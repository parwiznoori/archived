<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Grade;
use App\Models\Province;
use App\Models\Student;
use App\Models\StudentStatus;
use App\Models\Transfert;
use App\Models\University;
use App\Services\StudentsQueryService;
use DB;
use Illuminate\Http\Request;

class StudentsStatisticsController extends Controller
{
    protected $studentsQueryService;
    public function __construct(StudentsQueryService $studentsQueryService)
    {
        $this->studentsQueryService = $studentsQueryService;
    }

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
        // $studentsArray['totalStudents'] =0;
        // $studentsArray['totalStudentsWithLeave']=0;
        // $studentsArray['totalStudentsWithDroupout']=0;
        // $studentsArray['totalStudentsWithTransferFrom']=0;
        // $studentsArray['totalStudentsWithTransferTo']=0;
        
        return view('statistics.students.index', [
            'title' => trans('general.statistics'),
            'description' => trans('general.student_statistics'),
            'universities' => $universities,
            'provinces' => Province::pluck('name','id'),
            'kankor_years' =>  Student::select('kankor_year')->distinct()->orderBy('kankor_year', 'desc')->pluck('kankor_year','kankor_year'),
            'kankorYear' => $kankorYear,
            'statuses' => StudentStatus::pluck('title', 'id'),
            'grades' => Grade::pluck('name', 'id'),
            'department' =>  $departments,
            'studentsArray' => $studentsArray,
            'has_student_statistics' => 0
        ]);
    }


    public function show(Request $request)
    { 
        $studentsArray = array();
        $Students = array();
        // $validatedData = $request->validate([
        //     'kankor_year' => 'required',                      
        // ]);
       
        $studentsArray['totalStudents']=$totalStudents = $this->studentsQueryService->get_total_students($request);
        // dd($totalStudents);
        $studentsArray['totalStudentsWithLeave']=$totalStudentsWithLeave = $this->studentsQueryService->get_total_students_with_leave($request);
        $studentsArray['totalStudentsWithDroupout']=$totalStudentsWithDroupout = $this->studentsQueryService->get_total_students_with_dropouts($request);
        $studentsArray['totalStudentsWithTransferFrom']=$totalStudentsWithTransferFrom = $this->studentsQueryService->get_total_students_with_transfers_from($request);
        $studentsArray['totalStudentsWithTransferTo']=$totalStudentsWithTransferTo = $this->studentsQueryService->get_total_students_with_transfers_to($request);

        foreach($totalStudents as $s)
        {
            $Students[$s->id]['id'] = $s->id;
            $Students[$s->id]['name'] = $s->name;
            $Students[$s->id]['totalStudents'] = $s->count_students;
            $Students[$s->id]['totalStudentsWithLeave'] = 0;
            $Students[$s->id]['totalStudentsWithDroupout'] = 0;
            $Students[$s->id]['totalStudentsWithTransferFrom'] = 0;
            $Students[$s->id]['totalStudentsWithTransferTo'] = 0;
        }

        foreach($totalStudents as $s)
        {
            foreach($totalStudentsWithLeave as $sl)
            {
                if($s->id == $sl->id)
                {
                    $Students[$s->id]['totalStudentsWithLeave'] = $sl->count_students;
                }

            }

            foreach($totalStudentsWithDroupout as $sd)
            {
                if($s->id == $sd->id)
                {
                    $Students[$s->id]['totalStudentsWithDroupout'] = $sd->count_students;
                }

            }

            foreach($totalStudentsWithTransferFrom as $stf)
            {
                if($s->id == $stf->id)
                {
                    $Students[$s->id]['totalStudentsWithTransferFrom'] = $stf->count_students;
                }

            }

            foreach($totalStudentsWithTransferTo as $stt)
            {
                if($s->id == $stt->id)
                {
                    $Students[$s->id]['totalStudentsWithTransferTo'] = $stt->count_students;
                }

            }
        }


        return view('statistics.students.show-statistics', [
            'title' => trans('general.statistics'),
            'description' => trans('general.student_statistics'),
            'universities' => University::pluck('name', 'id'),
            'provinces' => Province::pluck('name','id'),
            'kankor_years' =>  Student::select('kankor_year')->distinct()->orderBy('kankor_year', 'desc')->pluck('kankor_year','kankor_year'),
            'statuses' => StudentStatus::pluck('title', 'id'),
            'grades' => Grade::pluck('name', 'id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : [],
            'studentsArray' => $studentsArray,
            'students' => $Students,
            'has_student_statistics' => 1
        ]);
    }

    public function show_by_province(Request $request)
    { 
        $studentsArray=array();
        // $validatedData = $request->validate([
        //     'university' => 'required',
        //     'kankor_year' => 'required',                      
        // ]);
       
        //  This query is used to fetch data of a specific university grouped by provinces
        $totalStudents = $this->studentsQueryService->get_total_students_by_province($request);
        

        return view('statistics.students.ajax-by-province', [
            'title' => trans('general.statistics'),
            'description' => trans('general.student_statistics'),
            'universities' => University::pluck('name', 'id'),
            'provinces' => Province::pluck('name','id'),
            'kankor_years' =>  Student::select('kankor_year')->distinct()->orderBy('kankor_year', 'desc')->pluck('kankor_year','kankor_year'),
            'statuses' => StudentStatus::pluck('title', 'id'),
            'grades' => Grade::pluck('name', 'id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : [],
            'totalStudents' => $totalStudents,
            'has_student_statistics' => 1
        ]);
    }
    
    public function show_by_university(Request $request)
    { 
        $studentsArray=array();
        // $validatedData = $request->validate([
            
        //     'kankor_year' => 'required',                      
        // ]);
       
        //  This query is used to fetch data of a specific university grouped by provinces
        $totalStudents = $this->studentsQueryService->get_total_students_by_university($request);
        

        return view('statistics.students.ajax-by-university', [
            'title' => trans('general.statistics'),
            'description' => trans('general.student_statistics'),
            'universities' => University::pluck('name', 'id'),
            'provinces' => Province::pluck('name','id'),
            'kankor_years' =>  Student::select('kankor_year')->distinct()->orderBy('kankor_year', 'desc')->pluck('kankor_year','kankor_year'),
            'statuses' => StudentStatus::pluck('title', 'id'),
            'grades' => Grade::pluck('name', 'id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : [],
            'totalStudents' => $totalStudents,
            'has_student_statistics' => 1
        ]);
    }

    public function show_by_department(Request $request)
    { 
        $validatedData = $request->validate([
            'kankor_year' => 'required', 
            'university' => 'required'                     
        ]);
        $Students = array();
       
        $departments = Department::leftJoin('universities', 'departments.university_id', '=', 'universities.id')
        ->select('departments.id as department_id','departments.name as department_name','universities.id as id','universities.name')
        ->where('university_id',$request->university)
        ->whereNull('universities.deleted_at')
        ->whereNull('departments.deleted_at')
        ->orderBy('department_name');
       

        if (request()->department != null) {
            $departments->where('departments.id', '=',  request()->department);
        }
        $departments = $departments->get();
        
       
        
        $totalStudents = $this->studentsQueryService->get_total_students($request,'departments');
        $totalStudentsWithLeave = $this->studentsQueryService->get_total_students_with_leave($request,'departments');
        $totalStudentsWithDroupout = $this->studentsQueryService->get_total_students_with_dropouts($request,'departments');
        $totalStudentsWithTransferFrom = $this->studentsQueryService->get_total_students_with_transfers_from($request,'departments');
        $totalStudentsWithTransferTo = $this->studentsQueryService->get_total_students_with_transfers_to($request,'departments');


        foreach($departments as $s)
        {
            $Students[$s->department_id]['id'] = $s->id;
            $Students[$s->department_id]['name'] = $s->name;
            $Students[$s->department_id]['department_id'] = $s->department_id;
            $Students[$s->department_id]['department_name'] = $s->department_name;
            $Students[$s->department_id]['totalStudents'] = 0;
            $Students[$s->department_id]['totalStudentsWithLeave'] = 0;
            $Students[$s->department_id]['totalStudentsWithDroupout'] = 0;
            $Students[$s->department_id]['totalStudentsWithTransferFrom'] = 0;
            $Students[$s->department_id]['totalStudentsWithTransferTo'] = 0;
        }

        foreach($totalStudents as $s)
        {
            
            $Students[$s->department_id]['id'] = $s->id;
            $Students[$s->department_id]['name'] = $s->name;
            $Students[$s->department_id]['department_id'] = $s->department_id;
            $Students[$s->department_id]['department_name'] = $s->department_name;
            $Students[$s->department_id]['totalStudents'] = $s->count_students;
            $Students[$s->department_id]['totalStudentsWithLeave'] = 0;
            $Students[$s->department_id]['totalStudentsWithDroupout'] = 0;
            $Students[$s->department_id]['totalStudentsWithTransferFrom'] = 0;
            $Students[$s->department_id]['totalStudentsWithTransferTo'] = 0;
        }

       
        foreach($departments as $s)
        {
            foreach($totalStudentsWithLeave as $sl)
            {
                if($s->department_id == $sl->department_id)
                {
                    $Students[$s->department_id]['totalStudentsWithLeave'] = $sl->count_students;
                }

            }

            foreach($totalStudentsWithDroupout as $sd)
            {
                if($s->department_id == $sd->department_id)
                {
                    $Students[$s->department_id]['totalStudentsWithDroupout'] = $sd->count_students;
                }

            }

            foreach($totalStudentsWithTransferFrom as $stf)
            {
                if($s->department_id == $stf->department_id)
                {
                    $Students[$s->department_id]['totalStudentsWithTransferFrom'] = $stf->count_students;
                }

            }

            foreach($totalStudentsWithTransferTo as $stt)
            {
                if($s->department_id == $stt->department_id)
                {
                    $Students[$s->department_id]['totalStudentsWithTransferTo'] = $stt->count_students;
                }

            }
        }
       
        
        return view('statistics.students.ajax-by-department', [
            'title' => trans('general.statistics'),
            'description' => trans('general.student_statistics'),
            'universities' => University::pluck('name', 'id'),
            'provinces' => Province::pluck('name','id'),
            'kankor_years' =>  Student::select('kankor_year')->distinct()->orderBy('kankor_year', 'desc')->pluck('kankor_year','kankor_year'),
            'statuses' => StudentStatus::pluck('title', 'id'),
            'grades' => Grade::pluck('name', 'id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : [],
            'totalStudents' => $totalStudents,
            'students' => $Students,
            'has_student_statistics' => 1
        ]);
    }
    
    public function show_by_gender(Request $request)
    { 
        $studentsArray=array();
        // $validatedData = $request->validate([
        //     // 'university' => 'required',
        //     'kankor_year' => 'required',                      
        // ]);
       
        //  This query is used to fetch data of a specific university grouped by provinces
        $totalStudents = $this->studentsQueryService->get_total_students_by_gender($request);
        

        return view('statistics.students.ajax-by-gender', [
            'title' => trans('general.statistics'),
            'description' => trans('general.student_statistics'),
            'universities' => University::pluck('name', 'id'),
            'provinces' => Province::pluck('name','id'),
            'kankor_years' =>  Student::select('kankor_year')->distinct()->orderBy('kankor_year', 'desc')->pluck('kankor_year','kankor_year'),
            'statuses' => StudentStatus::pluck('title', 'id'),
            'grades' => Grade::pluck('name', 'id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : [],
            'totalStudents' => $totalStudents,
            'has_student_statistics' => 1
        ]);
    }

    public function show_by_grade(Request $request)
    { 
        $studentsArray=array();
        // $validatedData = $request->validate([
        //     // 'university' => 'required',
        //     'kankor_year' => 'required',                      
        // ]);
       
        //  This query is used to fetch data of a specific university grouped by provinces
        $totalStudents = $this->studentsQueryService->get_total_students_by_grade($request);
        

        return view('statistics.students.ajax-by-grade', [
            'title' => trans('general.statistics'),
            'description' => trans('general.student_statistics'),
            'universities' => University::pluck('name', 'id'),
            'provinces' => Province::pluck('name','id'),
            'kankor_years' =>  Student::select('kankor_year')->distinct()->orderBy('kankor_year', 'desc')->pluck('kankor_year','kankor_year'),
            'statuses' => StudentStatus::pluck('title', 'id'),
            'grades' => Grade::pluck('name', 'id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : [],
            'totalStudents' => $totalStudents,
            'has_student_statistics' => 1
        ]);
    }
}
