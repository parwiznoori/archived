<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Imports\ScoreImportClass;
use App\Models\Course;
use App\Models\LessonWeek;
use App\Models\Score;
use App\Models\SystemVariable;
use Excel;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected $systemVariable;
    protected $MAX_MIDTERM_SCORE;
    protected $MAX_HOMEWORK_SCORE;
    protected $MAX_FINAL_SCORE;
    protected $MAX_CLASSWORK_SCORE;
    protected $MIN_PRESENT_PER_SUBJECT;
    protected $MIN_SCORE_FOR_PASSED_EXAM;
    
    protected $educationYear;
    protected $halfYear;
    protected $year;
    protected $semester;
    protected $teacherFullName;
    protected $departmentId;
    protected $universityName;
    protected $facultyName;
    protected $lessonWeek;
    protected $numberOfCredits;
    protected $sessionPerSemester;
    protected $sessionPerSemesterDefaultValue;
    protected $maxPresentOverAll;
    protected $maxPresent;
    protected $maxAbsent;
    protected $subjectTitle;
    protected $subjectDepartmentName;
    protected $courseDepartmentName;
    protected $subjectDepartmentId;
    protected $courseDepartmentId; 
    protected $courseStatusName;
    protected $numberOfCourseWeeks;
    protected $numberOfCourseHours;
    protected $legalMaxAbsentHours;


    public function __construct()
    {    
        $this->systemVariable=SystemVariable::where('name','NUMBWR_OF_SESSIONS_PER_SEMESTER')->first();
        $this->MAX_MIDTERM_SCORE=SystemVariable::where('name','MAX_MIDTERM_SCORE')->first();
        $this->MAX_HOMEWORK_SCORE=SystemVariable::where('name','MAX_HOMEWORK_SCORE')->first();
        $this->MAX_FINAL_SCORE=SystemVariable::where('name','MAX_FINAL_SCORE')->first();
        $this->MAX_CLASSWORK_SCORE=SystemVariable::where('name','MAX_CLASSWORK_SCORE')->first();
        $this->MIN_PRESENT_PER_SUBJECT=SystemVariable::where('name','MIN_PRESENT_PER_SUBJECT')->first();
        $this->MIN_SCORE_FOR_PASSED_EXAM=SystemVariable::where('name','MIN_SCORE_FOR_PASSED_EXAM')->first();
    }

    public function set_parameters(Course $course)
    {
        $this->universityName = $course->university->name;
        $this->facultyName= $course->department->facultyName->name;
        $this->subjectTitle = $course->subject->title;
        $this->courseStatusName =  $course->course_status_id ? $course->course_status->name : null;
        $this->year = $course->year;
        $this->semester = $course->semester;
        $this->teacherFullName = $course->teacher->full_name;
        $this->subjectDepartmentName = $course->subject->department->name;
        $this->courseDepartmentName =  $course->department->name;
        $this->subjectDepartmentId = $course->subject->department->id;
        $this->courseDepartmentId =  $course->department->id;
        $this->educationYear = $course->year;
        $this->halfYear = $course->half_year;
        $this->departmentId  = $course->department_id;
        $this->lessonWeek = LessonWeek::where('education_year',$course->year)
        ->where('half_year',$course->half_year)
        ->where('department_id',$course->department_id)
        ->select('number_of_weeks')
        ->first();
        $this->numberOfCredits=(int)$course->subject->credits;
        $session_per_semester=16;
        $this->sessionPerSemesterDefaultValue = $this->systemVariable->default_value;
       
        if(isset($this->systemVariable->user_value))
        {
            $session_per_semester=$this->systemVariable->user_value;
        }
        else if(isset($this->systemVariable->default_value))
        {
            $session_per_semester=$this->systemVariable->default_value;
        }
        else 
        {
            $session_per_semester=16;
        }
        $this->sessionPerSemester = $session_per_semester;
        $this->maxPresent=$this->sessionPerSemester * $this->numberOfCredits;
        $this->maxPresentOverAll=$this->sessionPerSemesterDefaultValue * $this->numberOfCredits;
        $this->maxAbsent= ((100- $this->MIN_PRESENT_PER_SUBJECT->user_value)*($this->maxPresent))/100 ;

        $this->numberOfCourseWeeks = $this->lessonWeek ? $this->lessonWeek->number_of_weeks : $this->sessionPerSemester;
        $this->numberOfCourseHours = $this->numberOfCourseWeeks * $this->numberOfCredits;
        $this->legalMaxAbsentHours = ((100- $this->MIN_PRESENT_PER_SUBJECT->user_value)*($this->numberOfCourseHours))/100 ;

    }

    public function get_parameters(Course $course, $title, $description)
    {  
        $parameters = [ //course.attendance.list
            'title' => $title,
            'description' => $description,
            'course' => $course,
            'systemVariable' => $this->systemVariable,
            'MAX_MIDTERM_SCORE' => $this->MAX_MIDTERM_SCORE->user_value,
            'MAX_HOMEWORK_SCORE' => $this->MAX_HOMEWORK_SCORE->user_value,
            'MAX_FINAL_SCORE' => $this->MAX_FINAL_SCORE->user_value,
            'MAX_CLASSWORK_SCORE' => $this->MAX_CLASSWORK_SCORE->user_value,
            'MIN_PRESENT_PER_SUBJECT' => $this->MIN_PRESENT_PER_SUBJECT->user_value,
            'NUMBWR_OF_SESSIONS_PER_SEMESTER' => $this->systemVariable->user_value,
            'MIN_SCORE_FOR_PASSED_EXAM' => $this->MIN_SCORE_FOR_PASSED_EXAM->user_value,
            'numberOfCredits' => $this->numberOfCredits,
            'session_per_semester_default_value' => $this->systemVariable->default_value,
            'lesson_week' => $this->lessonWeek,
            'session_per_semester' => $this->sessionPerSemester,
            'max_present_over_all' => $this->maxPresentOverAll,
            'max_present' => $this->maxPresent,
            'max_absent' =>  $this->maxAbsent,
            'subjectTitle' => $this->subjectTitle,
            'courseDepartmentId' => $this->courseDepartmentId,
            'courseDepartmentName' => $this->courseDepartmentName,
            'subjectDepartmentId' => $this->subjectDepartmentId,
            'subjectDepartmentName' => $this->subjectDepartmentName,
            'teacherFullName' => $this->teacherFullName,
            'semester' => $this->semester,
            'year' => $this->year,
            'halfYear' => $this->halfYear,
            'courseStatusName' => $this->courseStatusName,
            'numberOfCourseWeeks' => $this->numberOfCourseWeeks,
            'numberOfCourseHours' => $this->numberOfCourseHours,
            'legalMaxAbsentHours' => $this->legalMaxAbsentHours,
            'universityName' => $this->universityName,
            'facultyName' => $this->facultyName,
        ];
        return $parameters;
    }

    public function parameters(Course $course, $title, $description)
    { 
        $this->lessonWeek = LessonWeek::where('education_year',$course->year)
        ->where('half_year',$course->half_year)
        ->where('department_id',$course->department_id)
        ->select('number_of_weeks')
        ->first();

        $this->numberOfCredits=(int)$course->subject->credits;
        $session_per_semester=16;
        $this->sessionPerSemesterDefaultValue = $this->systemVariable->default_value;
       
        if(isset($this->systemVariable->user_value))
        {
            $session_per_semester=$this->systemVariable->user_value;
        }
        else if(isset($this->systemVariable->default_value))
        {
            $session_per_semester=$this->systemVariable->default_value;
        }
        else 
        {
            $session_per_semester=16;
        }
        $this->sessionPerSemester = $session_per_semester;
        $this->maxPresent=$this->sessionPerSemester * $this->numberOfCredits;
        $this->maxPresentOverAll=$this->sessionPerSemesterDefaultValue * $this->numberOfCredits;
        $this->maxAbsent= ((100- $this->MIN_PRESENT_PER_SUBJECT->user_value)*($this->maxPresent))/100 ;

        $this->numberOfCourseWeeks = $this->lessonWeek ? $this->lessonWeek->number_of_weeks : $this->sessionPerSemester;
        $this->numberOfCourseHours = $this->numberOfCourseWeeks * $this->numberOfCredits;
        $this->legalMaxAbsentHours = ((100- $this->MIN_PRESENT_PER_SUBJECT->user_value)*($this->numberOfCourseHours))/100 ;

        $parameters = [ //course.attendance.list
            'title' => $title,
            'description' => $description,
            'course' => $course,
            'systemVariable' => $this->systemVariable,
            'MAX_MIDTERM_SCORE' => $this->MAX_MIDTERM_SCORE->user_value,
            'MAX_HOMEWORK_SCORE' => $this->MAX_HOMEWORK_SCORE->user_value,
            'MAX_FINAL_SCORE' => $this->MAX_FINAL_SCORE->user_value,
            'MAX_CLASSWORK_SCORE' => $this->MAX_CLASSWORK_SCORE->user_value,
            'MIN_PRESENT_PER_SUBJECT' => $this->MIN_PRESENT_PER_SUBJECT->user_value,
            'NUMBWR_OF_SESSIONS_PER_SEMESTER' => $this->systemVariable->user_value,
            'MIN_SCORE_FOR_PASSED_EXAM' => $this->MIN_SCORE_FOR_PASSED_EXAM->user_value,
            'session_per_semester_default_value' => $this->systemVariable->default_value,
            'numberOfCredits' => $this->numberOfCredits,
            'lesson_week' => $this->lessonWeek,
            'session_per_semester' => $this->sessionPerSemester,
            'max_present_over_all' => $this->maxPresentOverAll,
            'max_present' => $this->maxPresent,
            'max_absent' =>  $this->maxAbsent,
            'subjectTitle' => $course->subject->title,
            'courseDepartmentId' => $course->department_id,
            'courseDepartmentName' => $course->department->name,
            'subjectDepartmentId' => $course->subject->department->id,
            'subjectDepartmentName' => $course->subject->department->name,
            'teacherFullName' => $course->teacher->full_name,
            'semester' => $course->semester,
            'year' => $course->year,
            'halfYear' => $course->half_year,
            'courseStatusName' => $course->course_status_id ? $course->course_status->name : null,
            'numberOfCourseWeeks' => $this->numberOfCourseWeeks,
            'numberOfCourseHours' => $this->numberOfCourseHours,
            'legalMaxAbsentHours' => $this->legalMaxAbsentHours,
            'universityName' => $course->university->name,
            'facultyName' => $course->department->facultyName->name,
            'isTeacher' => true,
        ];
        return $parameters;
    }

    public function list(Course $course)
    {
        $course->loadStudentsAndScoresAndSemesterDeprived();
        $parameters = $this->parameters($course, trans('general.attendance'),  trans('general.list'));
        return view('course.attendance.list_students', $parameters);
    }

    public function midterm(Course $course)
    {
        $course->loadStudentsAndScoresAndSemesterDeprived();
        $parameters = $this->parameters($course, trans('general.attendance'),  trans('general.midterm'));
        return view('course.scores.midterm-exam', $parameters);
    }

    public function deprived_student(Course $course)
    {
        $course->loadStudentsAndScoresAndSemesterDeprived();
        $parameters = $this->parameters($course, trans('general.attendance'),  trans('general.deprived'));
        return view('course.scores.deprived-student', $parameters);
    }

    public function absent_student(Course $course)
    {
        $course->loadStudentsAndScoresAndSemesterDeprived();
        $parameters = $this->parameters($course, trans('general.attendance'),  trans('general.absent_exam'));
        return view('course.scores.absent-student', $parameters);
    }

    public function final_exam(Course $course)
    {
        $course->loadStudentsAndScoresAndSemesterDeprived();
        $parameters = $this->parameters($course, trans('general.attendance'),  trans('general.final'));
        return view('course.scores.final-exam', $parameters);
    }

    public function excuse_exam(Course $course)
    {
        $course->loadStudentsAndScoresAndSemesterDeprived();
        
         // list of students that absent in exam were listed for excuse-exam
         $scores=Score::with('course')->with('student')
         ->where('course_id',$course->id)
         ->where('absent_exam',1)
         // ->where('passed',0)
         // ->whereNull('chance_two')
         ->get();
        $parameters = $this->parameters($course, trans('general.attendance'),  trans('general.excuse_exam'));
        $value = ['scores' => $scores];
        $parameters = $parameters +  $value;
       
        return view('course.scores.excuse-exam', $parameters);
    }

    public function chance2_exam(Course $course)
    {
        $course_id=$course->id;
        $course->loadStudentsAndScoresAndSemesterDeprived();

        // $scores=$course->loadStudentsAndScoresAndSemesterDeprived();
        $min_score=$this->MIN_SCORE_FOR_PASSED_EXAM->user_value;
        $scores=Score::with('course')->with('student')
        ->where('course_id',$course_id)
        ->Where(function($query) use($min_score) 
        {
            $query->where(function($query) use($min_score)  
            {
                $query->where('total','<',$min_score)
                ->orWhere('deprived', 1)
                ->orWhere('final', null)
                ->orWhere('total', null)
                ->orWhere(function($query) use($min_score) 
                {
                    $query->where('absent_exam', 1)
                        ->where('total', '<', $min_score);//if student has excuse exam => scores set in final exam
                });     
            });       
        })
        ->get();
        
        $parameters = $this->parameters($course, trans('general.attendance'),  trans('general.chance_two'));
        $value = ['scores' => $scores];
        $parameters = $parameters +  $value;
       
        return view('course.scores.chance2-exam', $parameters);
    }

    public function chance3_exam(Course $course)
    {
        $course_id=$course->id;
        $course->loadStudentsAndScoresAndSemesterDeprived();
        $min_score=$this->MIN_SCORE_FOR_PASSED_EXAM->user_value;
        $scores=Score::with('course')->with('student')
        ->where('course_id',$course->id)
        ->Where(function($query) use($min_score) 
        {  
            $query->where('chance_two','<',$min_score)
            ->orWhere(function($query) use($min_score) 
            {
                $query->whereNull('chance_two')
                    ->where('total', '<', $min_score);//if student has excuse exam => scores set in final exam
            });    
        })
        ->get();
        
        $parameters = $this->parameters($course, trans('general.attendance'),  trans('general.chance_three'));
        $value = ['scores' => $scores];
        $parameters = $parameters +  $value;
       
        return view('course.scores.chance3-exam', $parameters);
    }

    public function chance4_exam(Course $course)
    {
        $course_id=$course->id;
        $course->loadStudentsAndScoresAndSemesterDeprived();
        $min_score=$this->MIN_SCORE_FOR_PASSED_EXAM->user_value;
        $scores=Score::with('course')->with('student')
        ->where('course_id',$course->id)
        ->where('chance_three','<',$min_score)
        ->get();

        $parameters = $this->parameters($course, trans('general.attendance'),  trans('general.chance_four'));
        $value = ['scores' => $scores];
        $parameters = $parameters +  $value;
       
        return view('course.scores.chance4-exam', $parameters);
    }

    public function all_chances_scores(Course $course)
    {
        $course_id=$course->id;
        $course->loadStudentsAndScoresAndSemesterDeprived();
        $min_score=$this->MIN_SCORE_FOR_PASSED_EXAM->user_value;
        $scores=Score::with('course')->with('student')
        ->where('course_id',$course_id)
        ->orderBy('student_id')
        ->get();
        
        $parameters = $this->parameters($course, trans('general.attendance'),  trans('general.view_all_chances_scores'));
        $value = ['scores' => $scores];
        $parameters = $parameters +  $value;
       
        return view('course.scores.all-chances-scores', $parameters);
    }


   

    public function print($course)
    {
        $pdf = \PDF::loadView('course.attendance.print', compact('course'), [], [
            'format' => 'A4-L'
        ]);

        return $pdf->stream($course->code.'.pdf');
    }

    public function chance1_approved(Course $course)
    {
        $course->loadStudentsAndScoresAndSemesterDeprived();
        $course_id=$course->id;
        $studentsInCourse=$course->students;
        $errors=array();
        $i=0;
        $j=0;
        foreach($studentsInCourse as $student)
        {
            $SemesterDeprived = $student->SemesterDeprived;
            $isStudentSemesterDeprived = count($SemesterDeprived) >= 1 ? true : false;
            if(!$isStudentSemesterDeprived)
            {
                $scoresInCourseCount=Score::where('course_id',$course_id)
                                    ->where('student_id',$student->id)->count();
                if($scoresInCourseCount == 0 || $scoresInCourseCount > 1 )
                {
                    $errors[$i]['form_no'] = $student->form_no;
                    $errors[$i]['count'] = $scoresInCourseCount;
                    $errors[$i]['has_not_score'] = 0;
                    $i++;
                }
                else if($scoresInCourseCount == 1)
                {
                    $scoresInCourse=Score::where('course_id',$course_id)
                                    ->where('student_id',$student->id)->first();
                    if($scoresInCourse->deprived == 0 && $scoresInCourse->absent_exam == 0 
                    && ( is_null($scoresInCourse->final) || is_null($scoresInCourse->total) ) )
                    {
                        $errors[$i]['form_no'] = $student->form_no;
                        $errors[$i]['count'] = $scoresInCourseCount;
                        $errors[$i]['has_not_score'] = 1;
                        $i++;

                    }

                }
            }
        }
       
        $parameters = $this->parameters($course, trans('general.attendance'),  trans('general.chance1_approved_by_teacher'));
        $value = ['errors' => $errors];
        $parameters = $parameters +  $value;
       
        return view('course.scores.chance1-approved-by-teacher', $parameters);
    }

    public function approved_chance1_by_teacher(Request $request, $course)
    {
        $course_id= $course->id;
        \DB::transaction(function () use ( $course_id){

            $course=Course::where('id',$course_id)->update(['approve_by_teacher' => 1]);

        });
        
        $returnData = array(
            'status' => 'success',
            'message' => trans('general.course_successfuly_approved_by_teacher')
        );
        return response()->json($returnData, 200);

    }

    public function import_chance1_from_excel(Course $course)
    {
        dd($course);
    }

    public function score_chance1_from_excel(Request $request, $course)
    {
       
       // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        // Get the uploaded file
        $file = $request->file('file');

        // Process the Excel file
        Excel::import(new ScoreImportClass, $file);

        // exit;

        return redirect()->back()->with('success', trans('Excel_file_imported_successfully'));
    }
}
