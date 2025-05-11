<?php

namespace App\Http\Controllers\Students;

use App\DataTables\GraduatedStudentsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\GraduatedStudent;
use App\Models\Group;
use App\Models\Student;
use App\Models\SystemVariable;
use App\Models\University;
use App\Services\StudentService;
use Illuminate\Http\Request;
use Maklad\Permission\Models\Permission;
use Maklad\Permission\Models\Role;
use PDF;

class GraduatedStudentsController extends Controller
{
    protected $studentService;
    public $MIN_YEAR_KANKOR;
    public $MAX_YEAR_KANKOR;
    public $MIN_SEMESTER;
    public $MAX_SEMESTER;
    public $MIN_SCORE_FOR_PASSED_SEMESTER;

    public function __construct()
    {        
         $this->middleware('permission:view-graduated-student', ['only' => ['index', 'show']]);        
         $this->middleware('permission:create-graduated-student', ['only' => ['create','store','show_form','showResult','change_status','manualGraduate','manualGraduateStore']]);
         $this->middleware('permission:edit-graduated-student', ['only' => ['edit','update']]);
         $this->middleware('permission:delete-graduated-student', ['only' => ['destroy']]);

         $this->MIN_YEAR_KANKOR=SystemVariable::where('name','MIN_YEAR_KANKOR')->first();
         $this->MAX_YEAR_KANKOR=SystemVariable::where('name','MAX_YEAR_KANKOR')->first();
         $this->MIN_SEMESTER=SystemVariable::where('name','MIN_SEMESTER')->first();
         $this->MAX_SEMESTER=SystemVariable::where('name','MAX_SEMESTER')->first();
         $this->MIN_SCORE_FOR_PASSED_SEMESTER=SystemVariable::where('name','MIN_SCORE_FOR_PASSED_SEMESTER')->first()->user_value;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(GraduatedStudentsDataTable $dataTable)
    {        
        return $dataTable->render('students.graduated_students.index', [
            'title' => trans('general.graduated_students'),
            'description' => trans('general.graduated_students_list')            
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       exit;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {        
       exit;
    }

    public function show_form()
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($graduatedStudent)
    {        
        exit();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($graduatedStudent)
    {
        $graduatedStudent = GraduatedStudent::find($graduatedStudent);
        $student = Student::where('id',$graduatedStudent->student_id)->first();

        $MIN_YEAR_KANKOR=SystemVariable::where('name','MIN_YEAR_KANKOR')->first();
        $MAX_YEAR_KANKOR=SystemVariable::where('name','MAX_YEAR_KANKOR')->first();

        $university_id=$graduatedStudent->university_id;
        $department_id=$graduatedStudent->department_id;
        $kankor_year = $graduatedStudent->kankor_year;
        $department = Department::find($department_id);
        $semestersByDepartment = ceil($department->number_of_semesters/2);
        
        $grade_id = $department->grade_id;
        // dd($graduatedStudent);
        return view('students.graduated_students.edit', [
            'title' => trans('general.graduated_students'),
            'description' => trans('general.edit_graduated_student'),
            'graduatedStudent' => $graduatedStudent,
            'student' =>  $student,
            'MIN_YEAR_KANKOR' => $MIN_YEAR_KANKOR->user_value ,
            'MAX_YEAR_KANKOR' => $MAX_YEAR_KANKOR->user_value ,
            'grade_id' => $grade_id
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $graduatedStudent)
    {

        $graduatedStudent = GraduatedStudent::find($graduatedStudent);
        $validatedData = $request->validate([
            'graduated_year' => 'required',
        ]);

        $graduatedStudent->update([
            'graduated_year' => $request->graduated_year,
            'description' => $request->description,
            'registeration_date' => $request->registeration_date,
            
            'received_diploma' => $request->has('received_diploma'),
            'received_diploma_date' => $request->received_diploma_date,
            'diploma_letter_number' => $request->diploma_letter_number,
            'diploma_letter_date' => $request->diploma_letter_date,
            'diploma_number' => $request->diploma_number,

            'received_certificate' => $request->has('received_certificate'),
            'received_certificate_date' => $request->received_certificate_date,
            'certificate_letter_number' => $request->certificate_letter_number,
            'certificate_letter_date' => $request->certificate_letter_date,
            
            'received_transcript_en' => $request->has('received_transcript_en'),
            'received_transcript_en_date' => $request->received_transcript_en_date,
            'transcript_en_letter_number' => $request->transcript_en_letter_number,
            'transcript_en_letter_date' => $request->transcript_en_letter_date,

            'received_transcript_da' => $request->has('received_transcript_da'),
            'received_transcript_da_date' => $request->received_transcript_da_date,
            'transcript_da_letter_number' => $request->transcript_da_letter_number,
            'transcript_da_letter_date' => $request->transcript_da_letter_date,

            'received_transcript_pa' => $request->has('received_transcript_pa'),
            'received_transcript_pa_date' => $request->received_transcript_pa_date,
            'transcript_pa_letter_number' => $request->transcript_pa_letter_number,
            'transcript_pa_letter_date' => $request->transcript_pa_letter_date,

            'hand_over_identity_card' => $request->has('hand_over_identity_card'),
            'hand_over_non_responsibility_form' => $request->has('hand_over_non_responsibility_form'),
        ]);        

        
        return redirect(route('graduated-students.index'))->with('message', 'اطلاعات '.$graduatedStudent->student->name.' موفقانه آبدیت شد.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($graduatedStudent)
    {
        $graduatedStudent = GraduatedStudent::find($graduatedStudent);
        $graduatedStudent->delete();

        return redirect(route('graduated-students.index'));
    }

    public function manualGraduate($student)
    {
        
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

    public function graduateResults($id)
    {
        $graduatedStudent = GraduatedStudent::findOrFail($id);
        $student_id = $graduatedStudent->student_id;
        $studentInstance = new StudentService($student_id);
        $student = $studentInstance->studentInformation();
        $monograph = $studentInstance->monographWithTeacher();
        $yearOfLeave = $studentInstance->getLeaveYear()->leave_year ?? 0 ;
        $std_tazkira = explode('!@#', $student->tazkira);
        $tazkira = $std_tazkira[3];
        $studentData = $studentInstance->graduateStudentResults();
        $transferDescribtion = $studentInstance->transferDescribtion();

        $studentScores = $studentData['studentScores'];
        $studentResult = $studentData['studentResult'];
        $educationalYearPerSemesters = $studentData['educationalYearPerSemesters'];
        $semestersCount = $studentData['semestersCount'];
        $scores = $studentData['scores'];
        $totalCreditsAllSemesters = $studentData['totalCreditsAllSemesters'];
        $totalScoresAllSemesters = $studentData['totalScoresAllSemesters'];
        $averageScoresAllSemesters = $studentData['averageScoresAllSemesters'];
        $maxSubjectsCount = $studentData['maxSubjectsCount'];
      
        // return view('students.graduate-results', [
        //     'title' => trans('general.students'),
        //     'description' => trans('general.students_list'),
        //     'graduatedStudent' => $graduatedStudent,
        //     'student' => $student,
        //     'yearOfLeave' => $yearOfLeave,
        //     'transferDescribtion' => $transferDescribtion,
        //     'educationalYearPerSemesters' => $educationalYearPerSemesters,
        //     'monograph' => $monograph,
        //     'scores' => $scores,
        //     'studentScores' => $studentScores,
        //     'semestersCount' => $semestersCount,
        //     'studentResult' => $studentResult,
        //     'tazkira' => $tazkira,
        //     'maxSubjectsCount' => $maxSubjectsCount,
        //     'averageScoresAllSemesters' => $averageScoresAllSemesters,
        //     'totalScoresAllSemesters' => $totalScoresAllSemesters,
        //     'totalCreditsAllSemesters' => $totalCreditsAllSemesters,
        // ]);

        $pdf = PDF::loadView('students.graduate-results', [
            'title' => trans('general.students'),
            'description' => trans('general.students_list'),
            'graduatedStudent' => $graduatedStudent,
            'student' => $student,
            'yearOfLeave' => $yearOfLeave,
            'transferDescribtion' => $transferDescribtion,
            'educationalYearPerSemesters' => $educationalYearPerSemesters,
            'monograph' => $monograph,
            'scores' => $scores,
            'studentScores' => $studentScores,
            'semestersCount' => $semestersCount,
            'studentResult' => $studentResult,
            'tazkira' => $tazkira,
            'maxSubjectsCount' => $maxSubjectsCount,
            'averageScoresAllSemesters' => $averageScoresAllSemesters,
            'totalScoresAllSemesters' => $totalScoresAllSemesters,
            'totalCreditsAllSemesters' => $totalCreditsAllSemesters,
        ], [], [
            'format' => 'A4-L'
          ]);

        return $pdf->stream('Graduate-result.pdf');

    }

}
