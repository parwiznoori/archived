<?php

namespace App\Http\Controllers\Course;

use App\DataTables\LessonWeekDataTable;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\LessonWeek;
use App\Models\SystemVariable;
use App\Models\University;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LessonWeekController extends Controller
{
    public function __construct()
    {        
         $this->middleware('permission:view-lesson-weeks', ['only' => ['index', 'show']]);        
         $this->middleware('permission:create-lesson-weeks', ['only' => ['create','store']]);
         $this->middleware('permission:edit-lesson-weeks', ['only' => ['edit','update']]);
         $this->middleware('permission:delete-lesson-weeks', ['only' => ['destroy']]);
         
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(LessonWeekDataTable $dataTable)
    {        
        return $dataTable->render('lesson-weeks.index', [
            'title' => trans('general.lesson_weeks'),
            'description' => trans('general.lesson_weeks_lists')            
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {    
        $MIN_YEAR_KANKOR=SystemVariable::where('name','MIN_YEAR_KANKOR')->first();
        $MAX_YEAR_KANKOR=SystemVariable::where('name','MAX_YEAR_KANKOR')->first();
        $NUMBWR_OF_SESSIONS_PER_SEMESTER=SystemVariable::where('name','NUMBWR_OF_SESSIONS_PER_SEMESTER')->first();
        $options = get_half_year_options();
        $date=explode(' ',jdate()); //current date and time
        $currentDate=explode('-',$date[0]); //current date 
        $university_id= auth()->user()->university_id;
       
        if($university_id > 0 )
        {
            $universities=University::where('id',$university_id)->pluck('name', 'id');
        }
        else{
            $universities=University::pluck('name', 'id');
        }

       //changed teacher code 
        return view('lesson-weeks.create', [
            'title' => trans('general.lesson_weeks'),
            'description' => trans('general.create_lesson_weeks'),
            'universities' => $universities,
            'departments' => Department::pluck('name', 'id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : [],
            'MIN_YEAR_KANKOR' => $MIN_YEAR_KANKOR->user_value ,
            'MAX_YEAR_KANKOR' => $MAX_YEAR_KANKOR->user_value ,
            'NUMBWR_OF_SESSIONS_PER_SEMESTER' => $NUMBWR_OF_SESSIONS_PER_SEMESTER,
            'currentDate' => $currentDate,
            'options' => $options
           
        ]);
    }
    
  // 'teachers' => Teacher::leftJoin('departments', 'departments.id', 'teachers.department_id')->
        // select(DB::Raw('concat_ws(" ",teachers.name , teachers.last_name ,teachers.father_name, departments.name) as name'), 'id')->pluck('name','id'),


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $department_id=$request->department_id;
        $education_year=$request->education_year;
        $half_year=$request->half_year;
        
        $request->validate([
            
            'half_year' => [
                'required',
                Rule::unique('lesson_weeks')->whereNull('deleted_at')
                 ->where(function ($query) use($department_id,$education_year,$half_year) {
                   return $query->where('department_id', $department_id)
                                ->where('education_year', $education_year)
                                ->where('half_year', $half_year)
                                ;
                })
            ],
            'university_id' => 'required',
            'department_id' => 'required',
            'education_year' => 'required',
            'number_of_weeks' => 'required',
        ]);
        
        \DB::transaction(function () use ($request) {
            // $department = Department::find($request->department);

            $course = LessonWeek::create([
                'university_id' => $request->university_id,
                'department_id' => $request->department_id,
                'education_year' => $request->education_year,
                'number_of_weeks' => $request->number_of_weeks,
                'half_year' => $request->half_year,
            ]);

        });

        // if ($request->has('next')) {
        //     return redirect()->back();
        // }

        return redirect(route('lesson_weeks.index'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($lesson_week_id)
    {
        $options = get_half_year_options();
        $lesson_week=LessonWeek::findOrFail($lesson_week_id);
        $university_id= auth()->user()->university_id;
       
        if($university_id > 0 )
        {
            $universities=University::where('id',$university_id)->pluck('name', 'id');
        }
        else{
        
            $universities=University::pluck('name', 'id');
        }

        $MIN_YEAR_KANKOR=SystemVariable::where('name','MIN_YEAR_KANKOR')->first();
        $MAX_YEAR_KANKOR=SystemVariable::where('name','MAX_YEAR_KANKOR')->first();
        $NUMBWR_OF_SESSIONS_PER_SEMESTER=SystemVariable::where('name','NUMBWR_OF_SESSIONS_PER_SEMESTER')->first();
       

       //changed teacher code 
        return view('lesson-weeks.edit', [
            'title' => trans('general.lesson_weeks'),
            'description' => trans('general.edit_lesson_weeks'),
            'lesson_week' => $lesson_week,
            'universities' => $universities,
            'departments' => Department::where('university_id',$lesson_week->university_id)->pluck('name', 'id'),
            'MIN_YEAR_KANKOR' => $MIN_YEAR_KANKOR->user_value ,
            'MAX_YEAR_KANKOR' => $MAX_YEAR_KANKOR->user_value ,
            'NUMBWR_OF_SESSIONS_PER_SEMESTER' => $NUMBWR_OF_SESSIONS_PER_SEMESTER,
            'options' => $options
        ]);
        
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $lesson_week_id)
    {
        $lesson_week=LessonWeek::findOrFail($lesson_week_id);

        $department_id=$request->department_id;
        $education_year=$request->education_year;
        $half_year=$request->half_year;
        
        $request->validate([
            'half_year' => [
                'required',
                Rule::unique('lesson_weeks')->whereNull('deleted_at')->ignore($lesson_week_id)
                 ->where(function ($query) use($department_id,$education_year,$half_year) {
                   return $query->where('department_id', $department_id)
                                ->where('education_year', $education_year)
                                ->where('half_year', $half_year)
                                ;
                })
            ],
            'university_id' => 'required',
            'department_id' => 'required',
            'education_year' => 'required',
            'number_of_weeks' => 'required',
        ]);
       
       
        $lesson_week->update([
            'university_id' => $request->university_id,
            'department_id' => $request->department_id,
            'education_year' => $request->education_year,
            'number_of_weeks' => $request->number_of_weeks,
            'half_year' => $request->half_year
        ]);
        // $lesson_week->students()->sync(Student::whereIn('group_id', $lesson_week->groups)->pluck('id'));

        return redirect(route('lesson_weeks.index'));
    }

   

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($course)
    {
        
        return redirect(route('lesson_weeks.index'));
        
    }

}
