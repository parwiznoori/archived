<?php

namespace App\Http\Controllers\Students\Graduate;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\GraduatedStudent;
use App\Models\Group;
use App\Models\Student;
use App\Models\SystemVariable;
use App\Models\University;
use App\Services\StudentService;
use Illuminate\Http\Request;


class CheckGraduateStudentsController extends Controller
{
    protected $studentService;
    public $MIN_YEAR_KANKOR;
    public $MAX_YEAR_KANKOR;
    public $MIN_SEMESTER;
    public $MAX_SEMESTER;

    public function __construct()
    {
        $this->MIN_YEAR_KANKOR=SystemVariable::where('name','MIN_YEAR_KANKOR')->first();
        $this->MAX_YEAR_KANKOR=SystemVariable::where('name','MAX_YEAR_KANKOR')->first();
        $this->MIN_SEMESTER=SystemVariable::where('name','MIN_SEMESTER')->first();
        $this->MAX_SEMESTER=SystemVariable::where('name','MAX_SEMESTER')->first();
    }

    // public function index()
    // {

      
    //     return view('students.results.show-form', [
    //         'title' => trans('general.students_result'),
    //         'description' => trans('general.create_students_result'),
    //         'universities' => University::pluck('name', 'id'),
    //         'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name',
    //             'id') : [],
    //         'groups' => old('groups') != '' ? Group::where('id', old('groups'))->pluck('name', 'id') : [],
    //         'MIN_YEAR_KANKOR' => $this->MIN_YEAR_KANKOR->user_value ,
    //         'MAX_YEAR_KANKOR' => $this->MAX_YEAR_KANKOR->user_value ,
    //         'MIN_SEMESTER' => $this->MIN_SEMESTER->user_value ,
    //         'MAX_SEMESTER' => $this->MAX_SEMESTER->user_value 

    //     ]);

    // }
   
    public function show()
    {
        return view('students.graduate.show_form', [
            'title' => trans('general.students_graduation'),
            'description' => trans('general.select_students_list_based_group'),
            'universities' => University::pluck('name', 'id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name',
                'id') : [],
            'groups' => old('groups') != '' ? Group::where('id', old('groups'))->pluck('name', 'id') : [],

        ]);
    }

    public function showResult(Request $request)
    {
        $MIN_YEAR_KANKOR=SystemVariable::where('name','MIN_YEAR_KANKOR')->first();
        $MAX_YEAR_KANKOR=SystemVariable::where('name','MAX_YEAR_KANKOR')->first();

        $studentInstance='';
        $studentsPrefernces= array();
        $university_id=$request->university;
        $department_id=$request->department;
        $group_id=$request->groups;

        $group= Group::find($request->groups);
        $kankor_year = $group->kankor_year;
        $department = Department::find($request->department);
        $university = University::find($request->university);

        $semestersByDepartment = ceil($department->number_of_semesters/2);
        $graduation_year = $kankor_year + $semestersByDepartment ;

        
       
        $students= Student :: where('university_id' , $university_id)
        ->where('department_id' , $department_id)
        ->where('group_id',$group_id)->orderBy('kankor_year')->orderBy('name')->get();

        foreach($students as $student)
        {
            $student_id=$student->id;
            $studentInstance= new StudentService($student_id);
            $monograph= $studentInstance->monograph();
            $studentResultsSemesterCount = $studentInstance->find_student_results();
            $studentSuccessCredits = $studentInstance->find_total_success_credits();
            

            if($monograph)
            {
                $studentsPrefernces[$student_id]['monograph']['has'] = 1;
                $studentsPrefernces[$student_id]['monograph']['title'] = $monograph->title;
                $studentsPrefernces[$student_id]['monograph']['defense_date'] = $monograph->defense_date;
            }
            else
            {
                $studentsPrefernces[$student_id]['monograph']['has'] = 0;
                $studentsPrefernces[$student_id]['monograph']['title']='';
                $studentsPrefernces[$student_id]['monograph']['defense_date']='';
            }
            $studentsPrefernces[$student_id]['studentResultsSemesterCount'] = $studentResultsSemesterCount;
            $studentsPrefernces[$student_id]['studentSuccessCredits'] = $studentSuccessCredits;
            
            $is_graduated_this_student = $studentInstance->is_graduated_this_student( $studentsPrefernces[$student_id]['monograph']['has'] ,$studentResultsSemesterCount , $studentSuccessCredits ,$department->min_credits_for_graduated ,$department->number_of_semesters  );
            $studentsPrefernces[$student_id]['is_graduated_this_student'] = $is_graduated_this_student;
    
        }

        
       
        if(count($students) ==0 ){
            echo "no students for this university and group";
            exit;
        }
        // dd($university_id ,$department_id , $group  , $students);
    
        return view('students.graduate.list_students_for_check', [
            'title' => trans('general.students_graduation'),
            'description' => trans('general.select_students_list_based_group'),
            'department' => $department,
            'university' => $university,
            'group' => $group,
            'students' => $students,
            'MIN_YEAR_KANKOR' => $MIN_YEAR_KANKOR->user_value ,
            'MAX_YEAR_KANKOR' => $MAX_YEAR_KANKOR->user_value ,
            'graduation_year' => $graduation_year,
            'studentsPrefernces' => $studentsPrefernces,
        ]);
    }

    public function change_status(Request $request)
    {
        $graduated_students=$request->get('graduate');
        $department_id= $request->department;
        $university_id= $request->university;
        $group_id= $request->group;
        $group = Group::where('id',$group_id)->first();
        $kankor_year = $group->kankor_year;
        $department= Department::where('id',$department_id)->first();
        $semestersByDepartment = ceil($department->number_of_semesters/2);
        $grade_id = $department->grade_id;
        $graduated_year = $request->graduated_year ??  $kankor_year + $semestersByDepartment ;

        // echo "u : $university_id - d: $department_id".' g: '.$group_id.'<br>';
        // echo " g year : $graduated_year <br>";
        // echo "values : ";
        // dd($graduated_students);
        // exit;
        $studentGraduatedCounter=0;
        
        foreach($graduated_students as $graduated_student)
        {
            if($graduated_student > 0)
            {
                $studentResult = GraduatedStudent::where('student_id', $graduated_student)->count();
                if($studentResult <= 0 )
                {
                    $studentGraduatedCounter++;
                    $student = GraduatedStudent::create([
                        'university_id' => $university_id,
                        'department_id' => $department_id,
                        'student_id' => $graduated_student,
                        'grade_id' => $grade_id,
                        'graduated_year' => $graduated_year
                    ]);
                }
            }

        }

        return redirect(route('graduate.check.form'))->with('message',
            trans('general.students_have_graduated', ['students' => $studentGraduatedCounter]));
    }
    public function manualGraduate($student)
    {
        // echo "student manula graduate";
        // dd($student);

        // if(isset($student->status->editable) and !$student->status->editable and !(auth()->user()->hasRole('super-admin')))
        // {
        //     abort(404);
        // }
        $MIN_YEAR_KANKOR=SystemVariable::where('name','MIN_YEAR_KANKOR')->first();
        $MAX_YEAR_KANKOR=SystemVariable::where('name','MAX_YEAR_KANKOR')->first();

        $university_id=$student->university_id;
        $department_id=$student->department_id;
        $kankor_year = $student->kankor_year;
        $department = Department::find($department_id);
        $semestersByDepartment = ceil($department->number_of_semesters/2);
        $graduation_year = $kankor_year + $semestersByDepartment ;
        $grade_id = $department->grade_id;

        return view('students.graduate.manual_graduate', [
            'title' => trans('general.students'),
            'description' => trans('general.add_individual_student_to_graduation'),
            'student' => $student,
            'MIN_YEAR_KANKOR' => $MIN_YEAR_KANKOR->user_value ,
            'MAX_YEAR_KANKOR' => $MAX_YEAR_KANKOR->user_value ,
            'graduation_year' => $graduation_year,
            'grade_id' => $grade_id
        ]);
    }
    public function manualGraduateStore(Request $request)
    {

        //  echo "graduate store";
        //  exit;
        // dd($request);
       
        // $validatedData = $request->validate([
        //     'student_id' => [
        //         'required',
        //         Rule::unique('graduated_students')->whereNull('deleted_at')
        //     ],
        //     'university_id' => 'required',
        //     'department_id' => 'required',
        //     'grade_id' => 'required',
        //     'graduated_year' => 'required'

        // ]);
        $student = Student::find($request->student_id);
        $graduate_student = GraduatedStudent::where('student_id',$request->student_id)->count();
        // echo " graduate_student : $graduate_student";
        // exit;
        $transaction = \DB::transaction(function () use ($request, $student,$graduate_student) {            
            if (!$graduate_student ) {
                GraduatedStudent::create([
                    'university_id' => $request->university_id, 
                    'department_id' => $request->department_id,
                    'student_id' => $request->student_id,
                    'grade_id' => $request->grade_id, 
                    'graduated_year' => $request->graduated_year,
                    'manual_graduated' => 1,
                    'description' => $request->description
                ]);
                $student->update([ 'status_id' => 5 ]);
        
                                               
            } 
        });

        if (!$graduate_student )
        {
            return redirect(route('graduated-students.index'))->with('message', 'اطلاعات '.$student->name.' موفقانه  در بخش فارغ التحصیلان اضافه شد.'); 
        }
        else{
           
            return redirect(route('graduated-students.index'))->with('message', 'اطلاعات '.$student->name.' قبلا در دیتابیس محصلان فارغ التحصیل موجود است'); 
        }
       

       
       
    }

}
