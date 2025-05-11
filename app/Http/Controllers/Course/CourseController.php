<?php

namespace App\Http\Controllers\Course;

use App\DataTables\CourseDataTable;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Day;
use App\Models\Department;
use App\Models\Group;
use App\Models\Score;
use App\Models\Student;
use App\Models\StudentSemesterScore;
use App\Models\Subject;
use App\Models\SystemVariable;
use App\Models\Teacher;
use App\Models\University;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function __construct()
    {        
         $this->middleware('permission:view-course', ['only' => ['index', 'show']]);        
         $this->middleware('permission:create-course', ['only' => ['create','store']]);
         $this->middleware('permission:edit-course', ['only' => ['edit','update', 'updateStatus','update_groups']]);
         $this->middleware('permission:delete-course', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CourseDataTable $dataTable)
    {        
        return $dataTable->render('course.index', [
            'title' => trans('general.courses'),
            'description' => trans('general.courses_list')            
        ]);
    }

    public function recover($id)
    {      
        $course=Course::where('id',$id)->withTrashed()->first();
        $groups=$course->groups;
       
        if(isset($course->deleted_at))
        {
            $course->restore();
            $course->students()->sync(Student::where('group_id', $groups)->pluck('id'));
        }
       
        return redirect(route('courses.index'));
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

        if($university_id > 0)
        {
            $universities =  University::where('id',$university_id)->pluck('name', 'id');
            $teachers = Teacher::select(DB::Raw('concat_ws(" ",name ," (تخلص : ", last_name, ")" ," (نام پدر : ", father_name, ")" ) as name'), 'id')->pluck('name','id');
        }
        else{
            $universities =  University::pluck('name', 'id');
            $teachers = [];
        }
        $options = get_half_year_options();
       
       //changed teacher code 
        return view('course.create', [
            'title' => trans('general.courses'),
            'description' => trans('general.create_course'),
            'universities' => $universities,
            'departments' => Department::pluck('name', 'id'),
            'teachers' => $teachers,
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : [],
            'subject' => [],
            'groups' => old('groups') != '' ? Group::where('id', old('groups'))->pluck('name', 'id') : [],
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
        $subject_id=$request->subject_id;
        $teacher_id=$request->teacher;
        $year=$request->year;
        $half_year=$request->half_year;
        $code = $request->code;
        
        $request->validate([
            'code' => [
                'required',
                Rule::unique('courses')->whereNull('deleted_at')
                ->where(function ($query) use($university_id) {
                    return $query->where('university_id', $university_id);
                })
            ],
            'subject_id' => [
                'required',
                Rule::unique('courses')->whereNull('deleted_at')
                 ->where(function ($query) use($department_id,$subject_id,$teacher_id,$year,$half_year,$code) {
                   return $query->where('department_id', $department_id)
                                ->where('subject_id', $subject_id)
                                ->where('teacher_id', $teacher_id)
                                ->where('year', $year)
                                ->where('half_year', $half_year)
                                ->where('code', $code)
                                ;
                })
            ],
            'year' => 'required',
            'university_id' => 'required',
            'department_id' => 'required',
            'semester' => 'required',
            'half_year' => 'required',
            'teacher' => 'required',
            'groups' => 'required' //|array
        ]);
        
        \DB::transaction(function () use ($request) {
            // $department = Department::find($request->department);
            $course = Course::create([
                'code' => $request->code,
                'year' => $request->year,
                'half_year' => $request->half_year,
                'semester' => $request->semester,
                'subject_id' => $request->subject_id,
                'teacher_id' => $request->teacher,
                'groups' => $request->groups,
                'university_id' => $request->university_id,
                'department_id' => $request->department_id,
                'active' => $request->has('active'),
            ]);
            $data=array();
            $i=0;
            $data[$i]=$request->groups;
            $course->groups()->sync($data);

            $course->students()->sync(Student::where('group_id', $request->groups)->pluck('id'));
        });

        return redirect(route('courses.index'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($course)
    {
        $options = get_half_year_options();
        $MIN_YEAR_KANKOR=SystemVariable::where('name','MIN_YEAR_KANKOR')->first();
        $MAX_YEAR_KANKOR=SystemVariable::where('name','MAX_YEAR_KANKOR')->first();
        $MIN_SEMESTER=SystemVariable::where('name','MIN_SEMESTER')->first();
        $MAX_SEMESTER=SystemVariable::where('name','MAX_SEMESTER')->first();
        $teacher_id = $course->teacher_id;
        $subject_id = $course->subject_id;
        $department_id=$course->department_id;
        $teacher = Teacher::select(DB::Raw('concat_ws(" ",name ,"- (تخلص )", last_name ,"- (نام پدر)", father_name) as name'), 'id')->pluck('name','id');
        $subject = Subject::select(DB::Raw('concat_ws(" ",code ,"-", title ,"-", credits) as name'), 'id')->where('department_id',$department_id)->pluck('name','id');
        $groupsList=Group::where('department_id',$department_id)->pluck('name','id');
        $studentsInCourse=$course->students()->get();
        $groups=$course->groups()->pluck('groups.name', 'groups.id');
        $selectedGroups=$course->groups()->pluck('groups.id');
        return view('course.edit', [
            'title' => trans('general.courses'),
            'description' => trans('general.edit_course'),
            'course' => $course,
            'department' => Department::pluck('name', 'id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : $course->department()->pluck('name', 'id'),
            'group' =>  $course->group()->pluck('name', 'id'),
            'groups' => $groups,
            'teacher' =>  $teacher,
            'teacher_id' =>  $teacher_id,
            'subject' =>  $subject,
            'subject_id' =>  $subject_id,
            'groupsList' => $groupsList,
            'selectedGroups' => $selectedGroups,
            //'subject' => old('subject') != '' ? Subject::where('id', old('subject'))->pluck('title', 'id') : $course->subject()->pluck('title', 'id'),
            //'teacher' => old('teacher') != '' ? Teacher::where('id', old('teacher'))->pluck('name','last_name','father_name', 'id') : $course->teacher()->pluck('name', 'id'),
            'days' => Day::pluck('day','id'),
            'studentsInCourse' => $studentsInCourse,
            'MIN_YEAR_KANKOR' => $MIN_YEAR_KANKOR->user_value,
            'MAX_YEAR_KANKOR' => $MAX_YEAR_KANKOR->user_value,
            'MIN_SEMESTER' => $MIN_SEMESTER->user_value,
            'MAX_SEMESTER' => $MAX_SEMESTER->user_value,
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
    public function update(Request $request, $course)
    {
        if($course->final_approved == 1)
        {
            Session::flash('message', trans('general.this_course_has_been_finilizd_and_can_not_be_edited'));
            return redirect(route('courses.index'));
        }
        $department_id=$request->department;
        $subject_id=$request->subject_id;
        $teacher_id=$request->teacher;
        $year=$request->year;
        $half_year=$request->half_year;
        $code = $request->code;
        $university_id = $request->university;

        $validatedData = $request->validate([
            'code' => [
                'required',
                Rule::unique('courses')->whereNull('deleted_at')->ignore($course->id)
                ->where(function ($query) use($university_id) {
                    return $query->where('university_id', $university_id);
                })
            ],
            'year' => 'required',
            'semester' => 'required',
            'half_year' => 'required',
            'subject_id' => [
                'required',
                Rule::unique('courses')->whereNull('deleted_at')->ignore($course->id)
                ->where(function ($query) use($department_id,$subject_id,$teacher_id,$year,$half_year,$code) {
                    return $query->where('department_id', $department_id)
                                 ->where('subject_id', $subject_id)
                                 ->where('teacher_id', $teacher_id)
                                 ->where('year', $year)
                                 ->where('half_year', $half_year)
                                 ->where('code', $code)
                                 ;
                })
            ],
            'teacher' => 'required',
            'university' => 'required'
        ]);

        $course->update([
            'code' => $request->code,
            'year' => $request->year,
            'half_year' => $request->half_year,
            'semester' => $request->semester,
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher,
            'university_id' => $request->university,
            'department_id' => $request->department,
            'active' => $request->has('active'),
        ]);
        // $course->students()->sync(Student::whereIn('group_id', $course->groups)->pluck('id'));

        Session::flash('message', trans('general.course_with_code_was_successfully_updated',['code' => $course->code]));
        return redirect(route('courses.index'));
    }

    public function update_groups(Request $request, $course)
    {
        if($course->course_status_id >= 1)
        {
            Session::flash('message', trans('general.this_course_has_been_finilizd_and_can_not_be_edited'));
            return redirect(route('courses.index'));
        }
        $groups=$request->groups;
        $validatedData = $request->validate([
            'groups' => 'required',
        ]);

        $course->update([
            'groups' => $request->groups,
        ]);
       
        
        if(is_array($groups))
        {
            $data=array();
            for($i=0;$i<count($groups);$i++)
            {
                $data[$i]=$groups[$i];
            }
           
            $course->groups()->sync($data);
        }
        else
        {
           
            $data=array();
            $i=0;
            $data[$i]=$groups;
            $course->groups()->sync($data);
                
        }
        

        // $course->students()->sync(Student::where('group_id', $request->groups)->pluck('id'));
        $course->students()->sync(Student::whereIn('group_id', $data)->pluck('id'));

        return redirect(route('courses.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($course)
    {
        if($course->scores->count() > 0)
        {
            Session::flash('message', trans('general.this_course_has_scores_and_can_not_be_deleted'));
            return redirect()->back();
           

        }
        else
        {
            \DB::transaction(function () use ($course) {
                $course->students()->sync([]);
                $course->delete();
            });
    
            return redirect(route('courses.index'));

        }
        
    }

    /**
     * Remove the scores from student result and changed course => final aproved to zero 
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function backToNormal($id)
    {
        $course=Course::where('id',$id)->withTrashed()->first();
        $scores=Score::where('course_id',$id)->get();
        $studentsSemesterRecords=StudentSemesterScore::where('course_id',$id)->get();
        // dd($course);

        if($course->course_status_id >= 1)
        {  
            \DB::transaction(function () use ($course,$scores,$studentsSemesterRecords) {
                
                $course->update(['course_status_id' => null , 'final_approved' => 0]);
                foreach($scores as $score)
                {
                    $score->update(['course_status_id' => null , 'final_approved' => 0]);
                }
                foreach($studentsSemesterRecords as $studentsSemesterRecord)
                {
                    $studentsSemesterRecord->forceDelete();
                }
            });
            return redirect(route('courses.index'));
           
        }
        else
        {
            Session::flash('message', trans('errors.this_course_was_not_final_aproved_before'));
            return redirect()->back();

        }
        
    }

     /**
     *  changed course teacher_approved to zero 
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeTeacherApproved($id)
    {
        $course=Course::where('id',$id)->withTrashed()->first();
        
        if($course->approve_by_teacher >= 1)
        { 
            \DB::transaction(function () use ($course) {
                $course->update(['approve_by_teacher' => 0 ]);
            });
            Session::flash('message', trans('general.this_course_successfully_removed_approve_by_teacher'));
            return redirect(route('courses.index'));
        }
        else
        {
            Session::flash('message', trans('errors.this_course_was_not_final_aproved_before'));
            return redirect()->back();
        }
    }
}
