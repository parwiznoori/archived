<?php

namespace App\Http\Controllers\Students;

use App\DataTables\SemesterDeprivedStudentDataTable;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\SemesterDeprivedStudent;
use App\Models\Student;
use App\Models\SystemVariable;
use App\Models\University;
use DB;
use Illuminate\Http\Request;

class SemesterDeprivesStudentController extends Controller
{
    public function __construct()
    {        
         $this->middleware('permission:view-semester-deprived', ['only' => ['index', 'show']]);        
         $this->middleware('permission:create-semester-deprived', ['only' => ['create','store']]);
         $this->middleware('permission:edit-semester-deprived', ['only' => ['edit','update']]);
         $this->middleware('permission:delete-semester-deprived', ['only' => ['destroy']]);
         
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SemesterDeprivedStudentDataTable $dataTable)
    {   
        return $dataTable->render('students.semester-deprived.index', [
            'title' => trans('general.semester_deprived'),
            'description' => trans('general.list')            
        ]);
    }

    public function recover($id)
    {
        $SemesterDeprivedStudent=SemesterDeprivedStudent::where('id',$id)->withTrashed()->first();
        if(isset($SemesterDeprivedStudent->deleted_at))
        {
            $SemesterDeprivedStudent->restore();
        }
        return redirect(route('semester-deprived-student.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function create()
    {    
        $university_id= auth()->user()->university_id;
        $MIN_YEAR_KANKOR=SystemVariable::where('name','MIN_YEAR_KANKOR')->first();
        $MAX_YEAR_KANKOR=SystemVariable::where('name','MAX_YEAR_KANKOR')->first();
        $MIN_SEMESTER=SystemVariable::where('name','MIN_SEMESTER')->first();
        $MAX_SEMESTER=SystemVariable::where('name','MAX_SEMESTER')->first();
        $date=explode(' ',jdate()); //current date and time
        $currentDate=explode('-',$date[0]); //current date 
       
       if($university_id > 0 )
       {
            $universities=University::where('id',$university_id)->pluck('name', 'id');
       }
       else{
        $universities=University::pluck('name', 'id');
       }
       $options = get_half_year_options();
       //changed teacher code 
        return view('students.semester-deprived.create', [
            'title' => trans('general.semester_deprived'),
            'description' => trans('general.create'),
            'universities' => $universities,
            'departments' => Department::pluck('name', 'id'),
            'students' => old('students') != '' ? Student::where('id', old('students'))->pluck('name', 'id') : [],
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : [],
            'MIN_YEAR_KANKOR' => $MIN_YEAR_KANKOR->user_value ,
            'MAX_YEAR_KANKOR' => $MAX_YEAR_KANKOR->user_value ,
            'MIN_SEMESTER' => $MIN_SEMESTER->user_value ,
            'MAX_SEMESTER' => $MAX_SEMESTER->user_value ,
            'currentDate' => $currentDate,
            'options' => $options
        ]);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $university_id=$request->university_id;
        $department_id=$request->department_id;
        $educational_year=$request->educational_year;
        $half_year=$request->half_year;
        $student_id = $request->student_id;
        
        $request->validate([
            'student_id' => [
                'required',
                // Rule::unique('semester_deprived_students')->whereNull('deleted_at')
            ],
            'university_id' => 'required',
            'department_id' => 'required',
            'educational_year' => 'required',
            'half_year' => 'required',
            'semester' => 'required',
        ]);
        
        \DB::transaction(function () use ($request) {
            // $department = Department::find($request->department);

            $semester_deprived = SemesterDeprivedStudent::create([
                'university_id' => $request->university_id,
                'department_id' => $request->department_id,
                'student_id' => $request->student_id,
                'semester' => $request->semester,
                'half_year' => $request->half_year,
                'educational_year' =>  $request->educational_year,
            ]);
        });

        return redirect(route('semester-deprived-student.index'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($semester_deprived_student_id)
    {
        $semester_deprived=SemesterDeprivedStudent::findOrFail($semester_deprived_student_id);
        $university_id= auth()->user()->university_id;
        $options = get_half_year_options();
        $MIN_YEAR_KANKOR=SystemVariable::where('name','MIN_YEAR_KANKOR')->first();
        $MAX_YEAR_KANKOR=SystemVariable::where('name','MAX_YEAR_KANKOR')->first();
        $MIN_SEMESTER=SystemVariable::where('name','MIN_SEMESTER')->first();
        $MAX_SEMESTER=SystemVariable::where('name','MAX_SEMESTER')->first();
       
        if($university_id > 0 )
        {
            $universities=University::where('id',$university_id)->pluck('name', 'id');
        }
        else
        {
            $universities=University::pluck('name', 'id');
        }
        

        return view('students.semester-deprived.edit', [
            'title' => trans('general.semester_deprived'),
            'description' => trans('general.edit'),
            'semester_deprived' => $semester_deprived,
            'universities' => $universities,
            'departments' => Department::where('university_id',$semester_deprived->university_id)->pluck('name', 'id'),
            'MIN_YEAR_KANKOR' => $MIN_YEAR_KANKOR->user_value ,
            'MAX_YEAR_KANKOR' => $MAX_YEAR_KANKOR->user_value ,
            'MIN_SEMESTER' => $MIN_SEMESTER->user_value ,
            'MAX_SEMESTER' => $MAX_SEMESTER->user_value ,
            'options' => $options,
            'students' =>  Student::select('id', \DB::raw('CONCAT(form_no, " ", name, " ", last_name ,"-",father_name) as name'))->where('id',$semester_deprived->student_id)->pluck('name', 'id')
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $semester_deprived_student_id)
    {
        $semester_deprived=SemesterDeprivedStudent::findOrFail($semester_deprived_student_id);
       
        $request->validate([
            'student_id' => [
                'required',
                // Rule::unique('semester_deprived')->whereNull('deleted_at')->ignore($semester_deprived_student_id),
            ],
            'university_id' => 'required',
            'department_id' => 'required',
            'educational_year' => 'required',
            'half_year' => 'required',
            'semester' => 'required',
        ]);

        $score = number_format($request->score, 2);
       
        $semester_deprived->update([
            'university_id' => $request->university_id,
            'department_id' => $request->department_id,
            'student_id' => $request->student_id,
            'semester' => $request->semester,
            'half_year' => $request->half_year,
            'educational_year' =>  $request->educational_year,
        ]);
        return redirect(route('semester-deprived-student.index'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($semester_deprived_student_id)
    {
        $semester_deprived=SemesterDeprivedStudent::findOrFail($semester_deprived_student_id);

            \DB::transaction(function () use ($semester_deprived) {

                $semester_deprived->delete();
            });
    
            return redirect(route('semester-deprived-student.index'));
    }

    
}
