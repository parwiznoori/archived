<?php

namespace App\Http\Controllers\Students\Groups;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\GroupStudentHistory;
use App\Models\Student;
use App\Models\SystemVariable;
use App\Models\University;
use Illuminate\Http\Request;

class GroupListController extends Controller
{
    protected $MIN_YEAR_KANKOR;
    protected $MAX_YEAR_KANKOR;
    protected $MIN_SEMESTER;
    protected $MAX_SEMESTER;

    public function __construct()
    {
        $this->middleware('permission:group-view-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:group-add-student', ['only' => ['create', 'store']]);
        $this->middleware('permission:group-remove-student', ['only' => ['destroy']]);
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
    public function index($group)
    {
        return view('students.groups.list', [
            'title' => $group->name,
            'description' => trans('general.students_list'),
            'group' => $group,
            'universities' => University::pluck('name', 'id'),
            'department' => $group->department()->pluck('name', 'id'),
            'MIN_YEAR_KANKOR' => $this->MIN_YEAR_KANKOR ,
            'MAX_YEAR_KANKOR' => $this->MAX_YEAR_KANKOR ,
            'MIN_SEMESTER' => $this->MIN_SEMESTER ,
            'MAX_SEMESTER' => $this->MAX_SEMESTER ,
        ]);
    }
    /*
    return list of corses by group
    */
    public function courses_list($group)
    {
        $coursesPerSemesterList=array();
        $coursesSpecifications=array();
        $group_id=$group->id;
        $semestersList=Course::select(
            'courses.semester'
        )
        ->distinct()
        ->leftJoin('course_group', 'course_group.course_id', '=', 'courses.id')
        ->leftJoin('groups', 'groups.id', '=', 'course_group.group_id')
        ->where('groups.id',$group_id)
        ->groupBy('semester')
        ->orderBy('semester')
        ->get();

        foreach($semestersList as $semesterList)
        {
            $i=0;
            $totalSubjects=0;
            $totalCredits=0;
            $coursesList=Course::select(
                'courses.id',
                'courses.code',
                'courses.active',
                'courses.groups',
                'year',
                'half_year',
                'course_status_id',
                'courses.created_at',
                'courses.updated_at',
                'courses.deleted_at',
                'courses.semester',
                'courses.subject_id',
                'subjects.credits',
                'subjects.title as subject',
                'teachers.name as teacher',
                'courses.active',
                'course_status_id', 
            )
            ->leftJoin('course_group', 'course_group.course_id', '=', 'courses.id')
            ->leftJoin('groups', 'groups.id', '=', 'course_group.group_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'courses.subject_id')
            ->leftJoin('teachers', 'teachers.id', '=', 'courses.teacher_id')
            ->where('groups.id',$group_id)
            ->where('courses.semester',$semesterList->semester)
            ->orderBy('semester')
            ->get();

            foreach($coursesList as $course)
            {
                
                // $student_counts = DB::table('course_student')->where('course_id',$course->id)->count();
                $totalSubjects++;
                $totalCredits+=$course->credits;
                $coursesPerSemesterList[$semesterList->semester][$i]['id']=$course->id;
                $coursesPerSemesterList[$semesterList->semester][$i]['code']=$course->code;
                $coursesPerSemesterList[$semesterList->semester][$i]['year']=$course->year;
                $coursesPerSemesterList[$semesterList->semester][$i]['half_year']= __('general.'.$course->half_year);
                $coursesPerSemesterList[$semesterList->semester][$i]['subject']=$course->subject.'['.$course->subject_id.']';
                $coursesPerSemesterList[$semesterList->semester][$i]['credits']=$course->credits;
                $coursesPerSemesterList[$semesterList->semester][$i]['teacher']=$course->teacher;
                $coursesPerSemesterList[$semesterList->semester][$i]['active']=$course->active;
                $coursesPerSemesterList[$semesterList->semester][$i]['created_at']=$course->created_at;
                $coursesPerSemesterList[$semesterList->semester][$i]['updated_at']=$course->updated_at;
                $coursesPerSemesterList[$semesterList->semester][$i]['course_status_id']=$course->course_status_id;
                // $coursesPerSemesterList[$semesterList->semester][$i]['students_count']=$student_counts;

                
                $i++;
            }
            $coursesSpecifications[$semesterList->semester]['total_subjects']=$totalSubjects;
            $coursesSpecifications[$semesterList->semester]['total_credits']=$totalCredits;
        }
        
        return view('students.groups.course-list', [
            'title' => $group->name,
            'description' => trans('general.list_of_courses_in_group'),
            'group' => $group,
            'universities' => University::pluck('name', 'id'),
            'department' => $group->department()->pluck('name', 'id'),
            'MIN_YEAR_KANKOR' => $this->MIN_YEAR_KANKOR ,
            'MAX_YEAR_KANKOR' => $this->MAX_YEAR_KANKOR ,
            'MIN_SEMESTER' => $this->MIN_SEMESTER ,
            'MAX_SEMESTER' => $this->MAX_SEMESTER ,
            'coursesPerSemesterList' => $coursesPerSemesterList,
            'coursesSpecifications' => $coursesSpecifications,
            'semestersList' => $semestersList
        ]);

    }


    public function addStudent(Request $request, $group)
    {
        if ($request->has('student_id')) {

            $request->validate([
                'student_id' => 'required'
            ]);
            $student = Student::find($request->student_id);
            if($student->group_id)
            {

                return redirect(route('groups.list', $group))->with('message',  trans('general.this_student_already_has_group_you_must_first_remove_group', ['form_no' => $student->form_no , 'group_id' => $student->group_id]));
            }
            else
            {
                Student::where('id', $request->student_id)
                ->update(['group_id' => $group->id]);
            
                GroupStudentHistory::create([
                    'student_id' => $request->student_id,
                    'group_id' => $group->id,
                ]);
            }
            

        } elseif ($request->has('department_id')) {

            $request->validate([
                'department_id' => 'required',
                'kankor_year' => 'required',
            ]);

            if(isset($request->gender))
            {
                $students=Student::where([
                    'department_id' => $request->department_id,
                    'kankor_year' => $request->kankor_year,
                    'gender' => $request->gender,
                    'group_id' =>null
                ])->get();

               Student::where([
                    'department_id' => $request->department_id,
                    'kankor_year' => $request->kankor_year,
                    'gender' => $request->gender,
                    'group_id' =>null
                ])->update(['group_id' => $group->id]);
                
                if(count($students) > 0)
                {
                    foreach($students as $student)
                    {
                        GroupStudentHistory::create([
                            'student_id' => $student->id,
                            'group_id' => $group->id,
                        ]);
                    }
                }
            }
            else{
                $students=Student::where([
                    'department_id' => $request->department_id,
                    'kankor_year' => $request->kankor_year,
                    'group_id' =>null
                ])->get();
               Student::where([
                    'department_id' => $request->department_id,
                    'kankor_year' => $request->kankor_year,
                    'group_id' =>null
                ])->update(['group_id' => $group->id]);   
                
                if(count($students) > 0)
                {
                    foreach($students as $student)
                    {
                        GroupStudentHistory::create([
                            'student_id' => $student->id,
                            'group_id' => $group->id,
                        ]); 
                    }
                }
            }
           
        }

        return redirect(route('groups.list', $group));
    }


    public function removeStudent(Request $request, $group)
    {
        //dd($request->student_id);
        Student::where('id', $request->student_id)
            ->update([
                'group_id' => null
            ]);
        return redirect(route('groups.list', $group));
    }

}