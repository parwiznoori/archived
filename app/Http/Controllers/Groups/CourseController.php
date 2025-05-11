<?php

namespace App\Http\Controllers\Groups;

use App\DataTables\CourseDataTable;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Day;
use App\Models\Department;
use App\Models\Group;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\University;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function __construct()
    {        
         $this->middleware('permission:view-course', ['only' => ['index', 'show']]);        
         $this->middleware('permission:create-course', ['only' => ['create','store']]);
         $this->middleware('permission:edit-course', ['only' => ['edit','update', 'updateStatus']]);
         $this->middleware('permission:delete-course', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CourseDataTable $dataTable,$group)
    {     
       
        return $dataTable->render('groups.course.index', [
            'title' => trans('general.courses'),
            'description' => trans('general.courses_list')  ,
            'group' => $group ,          
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
        return view('course.create', [
            'title' => trans('general.courses'),
            'description' => trans('general.create_course'),
            'universities' => University::pluck('name', 'id'),
            'departments' => Department::pluck('name', 'id'),
            'teachers' => Teacher::select(DB::Raw('concat_ws(" ",name,last_name) as name'), 'id')->pluck('name','id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : [],
            'subject' => old('subject') != '' ? Subject::where('id', old('subject'))->pluck('title', 'id') : [],
            'groups' => old('groups') != '' ? Group::whereIn('id', old('groups'))->pluck('name', 'id') : []
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
        $request->validate([
            'code' => [
                'required',
                'max:10',
                Rule::unique('courses')->whereNull('deleted_at')
            ],
            'year' => 'required',
            'semester' => 'required',
            'half_year' => 'required',
            'subject' => 'required',
            'teacher' => 'required',
            'groups' => 'required|array|min:1'
        ]);
        
        \DB::transaction(function () use ($request) {
            $department = Department::find($request->department);

            $course = Course::create([
                'code' => $request->code,
                'year' => $request->year,
                'half_year' => $request->half_year,
                'semester' => $request->semester,
                'subject_id' => $request->subject,
                'teacher_id' => $request->teacher,
                'groups' => $request->groups,
                'university_id' => $department->university_id,
                'department_id' => $department->id,
                'active' => $request->has('active'),
            ]);

            $course->students()->sync(Student::whereIn('group_id', $course->groups)->pluck('id'));
        });

        if ($request->has('next')) {
            return redirect()->back();
        }

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
        
        return view('course.edit', [
            'title' => trans('general.courses'),
            'description' => trans('general.edit_course'),
            'course' => $course,
            'department' => Department::pluck('name', 'id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : $course->department()->pluck('name', 'id'),
            'group' =>  $course->group()->pluck('name', 'id'),
            'subject' => old('subject') != '' ? Subject::where('id', old('subject'))->pluck('title', 'id') : $course->subject()->pluck('title', 'id'),
            'teacher' => old('teacher') != '' ? Teacher::where('id', old('teacher'))->pluck('name', 'id') : $course->teacher()->pluck('name', 'id'),
            'days' => Day::pluck('day','id'),
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
        $validatedData = $request->validate([
            'code' => 'required|max:10',
            'year' => 'required',
            'semester' => 'required',
            'half_year' => 'required',
            'subject' => 'required',
            'teacher' => 'required',
            'university' => 'required'
        ]);

        $course->update([
            'code' => $request->code,
            'year' => $request->year,
            'half_year' => $request->half_year,
            'semester' => $request->semester,
            'subject_id' => $request->subject,
            'teacher_id' => $request->teacher,
            'university_id' => $request->university,
            'department_id' => $request->department,
            'active' => $request->has('active'),
        ]);
        $course->students()->sync(Student::whereIn('group_id', $course->groups)->pluck('id'));

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
        \DB::transaction(function () use ($course) {
            $course->students()->sync([]);
            $course->delete();
        });

        return redirect(route('courses.index'));
    }
}
