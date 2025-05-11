<?php

namespace App\Http\Controllers\Curriculum;

use App\DataTables\SubjectsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Subject;
use App\Models\SystemVariable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectsController extends Controller
{
    protected $MAX_CREDITS_FOR_EACH_SUBJECT;
    protected $MIN_YEAR_KANKOR;
    protected $MAX_YEAR_KANKOR;
    protected $MIN_SEMESTER;
    protected $MAX_SEMESTER;


    public function __construct()
    {        
        $this->middleware('permission:view-curriculum', ['only' => ['index', 'show']]);        
        $this->middleware('permission:create-curriculum', ['only' => ['create','store']]);
        $this->middleware('permission:edit-curriculum', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-curriculum', ['only' => ['destroy']]);
        $this->MAX_CREDITS_FOR_EACH_SUBJECT=SystemVariable::where('name','MAX_CREDITS_FOR_EACH_SUBJECT')->first()->user_value;
        $this->MIN_YEAR_KANKOR=SystemVariable::where('name','MIN_YEAR_KANKOR')->first()->user_value;
        $this->MAX_YEAR_KANKOR=SystemVariable::where('name','MAX_YEAR_KANKOR')->first()->user_value;
        $this->MIN_SEMESTER=SystemVariable::where('name','MIN_SEMESTER')->first()->user_value;
        $this->MAX_SEMESTER=SystemVariable::where('name','MAX_SEMESTER')->first()->user_value;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SubjectsDataTable $dataTable, $university, $department)
    {        
        return $dataTable->render('curriculum.subjects.index', [
            'title' => $university->name." - ".$department->name,
            'description' => trans('general.subjects_list'),
            'department' => $department,
            'university' => $university
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($university, $department)
    {
        return view('curriculum.subjects.create', [
            'title' => $university->name." - ".$department->name,
            'description' => trans('general.create_subject'),
            'department' => $department,
            'MAX_CREDITS_FOR_EACH_SUBJECT' => $this->MAX_CREDITS_FOR_EACH_SUBJECT,
            'MIN_YEAR_KANKOR' => $this->MIN_YEAR_KANKOR ,
            'MAX_YEAR_KANKOR' => $this->MAX_YEAR_KANKOR ,
            'MIN_SEMESTER' => $this->MIN_SEMESTER ,
            'MAX_SEMESTER' => $this->MAX_SEMESTER ,
            'university' => $university
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $university, $department)
    {
        $department_id=$department->id;
        $title=$request->title;
        $code=$request->code;

        $validatedData = $request->validate([
            'title' => [
                'required',
                Rule::unique('subjects')->whereNull('deleted_at')
                 ->where(function ($query) use($department_id,$title,$code) {
                   return $query->where('department_id', $department_id)
                                ->where('code', $code)
                                ->where('title', $title)
                                ;
                })
            ],
            'code' => [
                'required',
            ],
            'title_eng' => 'required',
            'credits' => 'required',
            'type' => 'required',
        ]);
        
        $subject = Subject::create([
            'university_id' => $university->id, 
            'department_id' => $department->id,
            'code' => $request->code, 
            'title' => $request->title, 
            'title_eng' => $request->title_eng,
            'credits' => $request->credits,
            'semester' => $request->semester,
            'type' => $request->type,
            'active' => $request->has('active'),
        ]);
        return redirect(route('subjects.index', [$university, $department]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($department)
    {
        exit();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($university, $department, $subject)
    {
       
        $courses_count = Subject::where('id',$subject->id)->withcount('courses')->first()->courses_count;
        return view('curriculum.subjects.edit', [
            'title' => $university->name." - ".$department->name,
            'description' => trans('general.edit_subject'),
            'subject' => $subject,
            'university' => $university,
            'MAX_CREDITS_FOR_EACH_SUBJECT' => $this->MAX_CREDITS_FOR_EACH_SUBJECT,
            'MIN_YEAR_KANKOR' => $this->MIN_YEAR_KANKOR ,
            'MAX_YEAR_KANKOR' => $this->MAX_YEAR_KANKOR ,
            'MIN_SEMESTER' => $this->MIN_SEMESTER ,
            'MAX_SEMESTER' => $this->MAX_SEMESTER ,
            'department' => $department,
            'courses_count' => $courses_count,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $university, $department, $subject)
    {
        $department_id=$department->id;
        $title=$request->title;
        $code=$request->code;

        $validatedData = $request->validate([
            'title' => [
                'required',
                Rule::unique('subjects')->whereNull('deleted_at')->ignore($subject->id)
                ->where(function ($query) use($department_id,$title,$code) {
                    return $query->where('department_id', $department_id)
                                 ->where('code', $code)
                                 ->where('title', $title)
                                 ;
                })
            ],
            'code' => [
                'required',
            ],
            'title_eng' => 'required',
            'credits' => 'required',
            'type' => 'required',
        ]);
        
        $subject->update([
            'code' => $request->code, 
            'title' => $request->title, 
            'title_eng' => $request->title_eng,
            'credits' => $request->credits,
            'semester' => $request->semester,
            'type' => $request->type,
            'active' => $request->has('active'),
        ]);       

        return redirect(route('subjects.index',[ $university, $department]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($university, $department, $subject)
    {
        $subject->delete();

        return redirect(route('subjects.index',[ $university, $department, $subject]));
    }

    /*
    return list of corses by subject
    */
    public function courses_list($university, $department, $subject)
    {
        $coursesBySubject=array();
        $i=0;
        $coursesList=Course::select(
            'courses.id',
            'courses.code',
            'courses.active',
            'courses.groups',
            'year',
            'half_year',
            'course_status_id',
            'courses.deleted_at',
            'courses.semester',
            'subjects.credits',
            'subjects.title as subject',
            'teachers.name as teacher',
            'courses.active',
            'course_status_id', 
            'departments.name as department'
        )
        ->leftJoin('subjects', 'subjects.id', '=', 'courses.subject_id')
        ->leftJoin('departments', 'departments.id', '=', 'courses.department_id')
        ->leftJoin('teachers', 'teachers.id', '=', 'courses.teacher_id')
        ->where('courses.subject_id',$subject->id)
        ->orderBy('courses.semester')
        ->get();

        foreach($coursesList as $course)
        {
            $coursesBySubject[$i]['id']=$course->id;
            $coursesBySubject[$i]['code']=$course->code;
            $coursesBySubject[$i]['year']=$course->year;
            $coursesBySubject[$i]['half_year']=$course->half_year;
            $coursesBySubject[$i]['semester']=$course->semester;
            $coursesBySubject[$i]['department']=$course->department;
            $coursesBySubject[$i]['subject']=$course->subject;
            $coursesBySubject[$i]['credits']=$course->credits;
            $coursesBySubject[$i]['teacher']=$course->teacher;
            $coursesBySubject[$i]['active']=$course->active;
            $coursesBySubject[$i]['course_status_id']=$course->course_status_id;
            $i++;
        }
       
        
        return view('curriculum.subjects.course-list', [
            'title' => $university->name." - ".$department->name,
            'description' => trans('general.list_of_courses_by_subject'),
            'coursesBySubject' => $coursesBySubject,
            'subject' => $subject,
        ]);

    }

    public function editCredit($university, $department,$id)
    {   
        // dd($university, $department,$id);
        $subject=Subject::where('id',$id)->first(); 
        $courses_count = Subject::where('id',$id)->withcount('courses')->first()->courses_count;
        if (auth()->user()->hasRole('super-admin')) {
            return view('curriculum.subjects.edit-credit-form', [
                'title' => $university->name." - ".$department->name,
                'description' => trans('general.edit_credit'),
                'subject' => $subject,
                'university' => $university,
                'MAX_CREDITS_FOR_EACH_SUBJECT' => $this->MAX_CREDITS_FOR_EACH_SUBJECT,
                'MIN_YEAR_KANKOR' => $this->MIN_YEAR_KANKOR ,
                'MAX_YEAR_KANKOR' => $this->MAX_YEAR_KANKOR ,
                'MIN_SEMESTER' => $this->MIN_SEMESTER ,
                'MAX_SEMESTER' => $this->MAX_SEMESTER ,
                'department' => $department,
                'courses_count' => $courses_count,
            ]);
        }
        
    }

    public function updateCredit(Request $request,$university, $department, $id)
    {   
        $subject=Subject::where('id',$id)->first();     
        \DB::transaction(function () use ($subject, $request) {
            $subject->update([
                'credits' => $request->credits
            ]);
        });

        return redirect(route('subjects.index',[ $university, $department]))->with('message', 'کریکلم با کد  '.$subject->code.' موفقانه تصحیح شد.');
    }

}
