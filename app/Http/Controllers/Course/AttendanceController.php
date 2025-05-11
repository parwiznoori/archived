<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Department;
use App\Models\LessonWeek;
use App\Models\Score;
use App\Models\StudentSemesterScore;
use App\Models\SystemVariable;
use App\Services\SemesterBasedResultsService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected $semesterBasedResultsService;
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

    protected $course;
    protected $courseStudentsAndScores;

    public function __construct(SemesterBasedResultsService $semesterBasedResultsService)
    {        
        $this->middleware('permission:edit-course', ['only' => ['removeStudent']]);
        $this->middleware('permission:view-course', ['only' => ['list', 'print']]);
        $this->semesterBasedResultsService = $semesterBasedResultsService;
        $system_variables = SystemVariable::select('name','default_value','user_value')->get();
       

        $this->systemVariable=$system_variables->where('name','NUMBWR_OF_SESSIONS_PER_SEMESTER')->first();

        $this->MAX_MIDTERM_SCORE=$system_variables->where('name','MAX_MIDTERM_SCORE')->first();
        $this->MAX_HOMEWORK_SCORE=$system_variables->where('name','MAX_HOMEWORK_SCORE')->first();
        $this->MAX_FINAL_SCORE=$system_variables->where('name','MAX_FINAL_SCORE')->first();
        $this->MAX_CLASSWORK_SCORE=$system_variables->where('name','MAX_CLASSWORK_SCORE')->first();
        $this->MIN_PRESENT_PER_SUBJECT=$system_variables->where('name','MIN_PRESENT_PER_SUBJECT')->first();
        $this->MIN_SCORE_FOR_PASSED_EXAM=$system_variables->where('name','MIN_SCORE_FOR_PASSED_EXAM')->first();
    }

    public function set_parameters(Course $course)
    {
        $this->course = $course->loadStudentsAndScoresAndSemesterDeprived();
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
        $parameters = [ 
            'title' => $title,
            'description' => $description,
            'course' => $this->course,
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
            'isTeacher' => false,
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

    public function all_chances_scores(Course $course)
    {
        $course_id=$course->id;
        $course->loadStudentsAndScoresAndSemesterDeprived();
        // dd($course);
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

    public function chance2_exam(Course $course)
    {
        $course_id=$course->id;
        $course->loadStudentsAndScoresAndSemesterDeprived();

        // $scores=$course->loadStudentsAndScoresAndSemesterDeprived();
        $min_score=$this->MIN_SCORE_FOR_PASSED_EXAM->user_value;
        $scores=Score::with('course')
        ->with('student')
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
    
    public function print($course)
    {
        $course->loadStudentsAndScoresAndSemesterDeprived();
        $this->set_parameters($course);
        $parameters = $this->get_parameters($course, trans('general.attendance'),  trans('general.list'));

        if($course->subject->credits >= 2){
            $pdf = \PDF::loadView('course.attendance.print', $parameters, [], [
                'format' => 'A4-L'
            ]);
        }
        else{
            $pdf = \PDF::loadView('course.attendance.print', $parameters, [], [
                // 'format' => 'A4-L'
            ]);
        }
       
        return $pdf->stream($course->code.'.pdf');
    }

    public function addStudent(Request $request, $course)
    {

        $request->validate([            
            'student_id' => 'required'
        ]);
        
        if($course->students->contains($request->student_id)){
            
            return redirect()->back()->withErrors([trans('general.student_exist_message')]);
        }
        $course->students()->attach($request->student_id);   

        return redirect()->back();
    }

    public function removeStudent(Request $request, $course)
    {
        \DB::transaction(function () use ($request, $course) {
            if($request->student_id)
            {
                $course->students()->detach($request->student_id);
                $course->scores()->where('student_id', $request->student_id)->delete();  
            }
               
        });

        return redirect()->back();
    }
    public function removeScore(Request $request, $course)
    {
        \DB::transaction(function () use ($request, $course) {
           
            $course->scores()->where('id', $request->score_id)->delete();     
        });

        return redirect()->back();
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
        //dd($errors);
        $parameters = $this->parameters($course, trans('general.attendance'),  trans('general.chance1_final_approved'));
        $value = ['errors' => $errors];
        $parameters = $parameters +  $value;
       
        return view('course.scores.chance1-approved', $parameters);
    }

    public function final_approved_chance1_insert_scores(Request $request, $course)
    {
        $course_id=$request->input('course_id'); 
        $systemVariable=SystemVariable::where('name','NUMBWR_OF_SESSIONS_PER_SEMESTER')->first();
        $MAX_MIDTERM_SCORE=SystemVariable::where('name','MAX_MIDTERM_SCORE')->first();
        $MAX_HOMEWORK_SCORE=SystemVariable::where('name','MAX_HOMEWORK_SCORE')->first();
        $MAX_FINAL_SCORE=SystemVariable::where('name','MAX_FINAL_SCORE')->first();
        $MAX_CLASSWORK_SCORE=SystemVariable::where('name','MAX_CLASSWORK_SCORE')->first();
        $MIN_PRESENT_PER_SUBJECT=SystemVariable::where('name','MIN_PRESENT_PER_SUBJECT')->first();
        $min_score=SystemVariable::where('name','MIN_SCORE_FOR_PASSED_EXAM')->first();

        $course->loadStudentsAndScoresAndSemesterDeprived();
        $MIN_SCORE_FOR_PASSED_EXAM=$min_score->user_value;
        $studentScoresArray=array();
        $index=0;
        $scores=Score::with('course')->with('student')
        ->where('course_id',$course_id)
        ->orderBy('student_id')
        ->get();

        foreach($scores as $studentScore)
        {
            $studentScoresArray[$index]['score_id']=$studentScore->id;
            $studentScoresArray[$index]['full_name']=$studentScore->student->full_name;
            $studentScoresArray[$index]['form_no']=$studentScore->student->form_no;
            $studentScoresArray[$index]['father_name']=$studentScore->student->father_name;
            $studentScoresArray[$index]['kankor_year']=$studentScore->student->kankor_year;

            $final_score=(isset($studentScore->final) ? $studentScore->final : null); //final exam score
            $present=(isset($studentScore->present) ? $studentScore->present : 0); //present
            $absent=(isset($studentScore->absent) ? $studentScore->absent : 0); //absent
            $absent_exam=(isset($studentScore->absent_exam) ? $studentScore->absent_exam : null); //absent_exam
            $excuse_exam=(isset($studentScore->excuse_exam) ? $studentScore->excuse_exam : null); //excuse_exam
            $deprived=(isset($studentScore->deprived) ? $studentScore->deprived : null); //deprived

            $studentScoresArray[$index]['chance_1']=
            (isset($studentScore->total) ? $studentScore->total : null); //chance 1
            $studentScoresArray[$index]['chance_2']= null;//chance 2
            $studentScoresArray[$index]['chance_3']= null;//chance 3
            $studentScoresArray[$index]['chance_4']= null;//chance 4

            $student_deprived=is_this_student_deprived_from_exam($present,$absent,$deprived);
            $studentScoresArray[$index]['deprived']=$student_deprived;

            $student_absent=is_this_student_absent_in_final_exam($final_score,$present,$absent,$absent_exam,$excuse_exam,$deprived);
            $studentScoresArray[$index]['absent_exam']=$student_absent;

            $studentScoresArray[$index]['excuse_exam']=$studentScore->excuse_exam;

            $passed_course=is_this_student_passed_this_course($studentScoresArray[$index]['chance_1'],$studentScoresArray[$index]['chance_2'],$studentScoresArray[$index]['chance_3'],$studentScoresArray[$index]['chance_4'],$MIN_SCORE_FOR_PASSED_EXAM);

            if($passed_course)
            {
                $studentScoresArray[$index]['passed']=1;
                /* data only inserted into student semester results when course was passed by chance 1 */ 
                $coursePassedData=$this->semesterBasedResultsService->which_chance_student_passed_course($studentScoresArray[$index]['chance_1'],$studentScoresArray[$index]['chance_2'],$studentScoresArray[$index]['chance_3'],$studentScoresArray[$index]['chance_4'],$MIN_SCORE_FOR_PASSED_EXAM);

                $student_id=$studentScore->student->id;
                $subject_id=$studentScore->course->subject->id;
                $score_id=$studentScore->id;
                $education_year=$studentScore->course->year;
                $semester=$studentScore->course->semester;
                
                $chance_one=$studentScoresArray[$index]['chance_1'];
                $chance_two=$studentScoresArray[$index]['chance_2'];
                $chance_three=$studentScoresArray[$index]['chance_3'];
                $chance_four=$studentScoresArray[$index]['chance_4'];
                $success_score=$coursePassedData['success_score'];
                $success_chance=$coursePassedData['success_chance'];

                \DB::transaction(function () use ($student_id,$subject_id,$education_year, $semester, $course_id, $score_id, $chance_one , $chance_two ,$chance_three ,$chance_four ,$success_score ,$success_chance){

                    $results=StudentSemesterScore::addSubjectScore($student_id,$subject_id,$education_year, $semester, $course_id, $score_id, $chance_one , $chance_two ,$chance_three ,$chance_four ,$success_score ,$success_chance);

                    $course=Course::where('id',$course_id)->update(['course_status_id' => 1]);

                    $scores=Score::where('id',$score_id)->update(['course_status_id' => 1]);
                    
                });
            }
            else
            {
                $studentScoresArray[$index]['passed']=0;
            }

            $index++;

        }
       

        return view('course.scores.ajax_students', [ //course.attendance.list
            'title' => trans('general.attendance'),
            'description' => trans('general.final_approved'),
            'course' => $course,
            'studentScoresArray' =>  $studentScoresArray,
            'systemVariable' => $systemVariable,
            'MAX_MIDTERM_SCORE' => $MAX_MIDTERM_SCORE->user_value,
            'MAX_HOMEWORK_SCORE' => $MAX_HOMEWORK_SCORE->user_value,
            'MAX_FINAL_SCORE' => $MAX_FINAL_SCORE->user_value,
            'MAX_CLASSWORK_SCORE' => $MAX_CLASSWORK_SCORE->user_value,
            'MIN_PRESENT_PER_SUBJECT' => $MIN_PRESENT_PER_SUBJECT->user_value,
            'NUMBWR_OF_SESSIONS_PER_SEMESTER' => $systemVariable->user_value,
            'MIN_SCORE_FOR_PASSED_EXAM' => $MIN_SCORE_FOR_PASSED_EXAM,
            
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : []
        ]);
    }

    public function chance2_approved(Course $course)
    {
        $course_id=$course->id;
        $min_score=$this->MIN_SCORE_FOR_PASSED_EXAM->user_value;

        $scores=Score::with('course')
        ->with('student')
        ->where('course_id',$course_id)
        // ->where('total','<',$MIN_SCORE_FOR_PASSED_EXAM->user_value)
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

       
        $course->loadStudentsAndScoresAndSemesterDeprived();
        $parameters = $this->parameters($course, trans('general.attendance'),  trans('general.chance2_final_approved'));
        $value = ['scores' => $scores];
        $parameters = $parameters +  $value;
       
        return view('course.scores.chance2-approved', $parameters);
    }

    public function final_approved_chance2_insert_scores(Request $request, $course)
    {
        $systemVariable=SystemVariable::where('name','NUMBWR_OF_SESSIONS_PER_SEMESTER')->first();

        $MAX_MIDTERM_SCORE=SystemVariable::where('name','MAX_MIDTERM_SCORE')->first();
        $MAX_HOMEWORK_SCORE=SystemVariable::where('name','MAX_HOMEWORK_SCORE')->first();
        $MAX_FINAL_SCORE=SystemVariable::where('name','MAX_FINAL_SCORE')->first();
        $MAX_CLASSWORK_SCORE=SystemVariable::where('name','MAX_CLASSWORK_SCORE')->first();
        $MIN_PRESENT_PER_SUBJECT=SystemVariable::where('name','MIN_PRESENT_PER_SUBJECT')->first();
        

        $course->loadStudentsAndScoresAndSemesterDeprived();
       
        $studentScoresArray=array();
        $index=0;

        $course_id=$course->id;
        $MIN_SCORE_FOR_PASSED_EXAM=$min_score=$this->MIN_SCORE_FOR_PASSED_EXAM->user_value;

        $scores=Score::with('course')
        ->with('student')
        ->where('course_id',$course_id)
        // ->where('total','<',$MIN_SCORE_FOR_PASSED_EXAM->user_value)
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

       

        foreach($scores as $studentScore)
        {
            $studentScoresArray[$index]['score_id']=$studentScore->id;
            $studentScoresArray[$index]['full_name']=$studentScore->student->full_name;
            $studentScoresArray[$index]['form_no']=$studentScore->student->form_no;
            $studentScoresArray[$index]['father_name']=$studentScore->student->father_name;
            $studentScoresArray[$index]['kankor_year']=$studentScore->student->kankor_year;

            $final_score=(isset($studentScore->final) ? $studentScore->final : null); //final exam score
            $present=(isset($studentScore->present) ? $studentScore->present : 0); //present
            $absent=(isset($studentScore->absent) ? $studentScore->absent : 0); //absent
            $absent_exam=(isset($studentScore->absent_exam) ? $studentScore->absent_exam : null); //absent_exam
            $excuse_exam=(isset($studentScore->excuse_exam) ? $studentScore->excuse_exam : null); //excuse_exam
            $deprived=(isset($studentScore->deprived) ? $studentScore->deprived : null); //deprived

            $studentScoresArray[$index]['chance_1']=
            (isset($studentScore->total) ? $studentScore->total : null); //chance 1
            $studentScoresArray[$index]['chance_2']= 
            (isset($studentScore->chance_two) ? $studentScore->chance_two : null);//chance 2
            $studentScoresArray[$index]['chance_3']= null;//chance 3
            $studentScoresArray[$index]['chance_4']= null;//chance 4

            $student_deprived=is_this_student_deprived_from_exam($present,$absent,$deprived);
            $studentScoresArray[$index]['deprived']=$student_deprived;

            $student_absent=is_this_student_absent_in_final_exam($final_score,$present,$absent,$absent_exam,$excuse_exam,$deprived);
            $studentScoresArray[$index]['absent_exam']=$student_absent;

            $studentScoresArray[$index]['excuse_exam']=$studentScore->excuse_exam;

            $passed_course=is_this_student_passed_this_course($studentScoresArray[$index]['chance_1'],$studentScoresArray[$index]['chance_2'],$studentScoresArray[$index]['chance_3'],$studentScoresArray[$index]['chance_4'],$MIN_SCORE_FOR_PASSED_EXAM);

            if($passed_course)
            {
                $studentScoresArray[$index]['passed']=1;
                

                /* data only inserted into student semester results when course was passed by chance 1 */ 
                $coursePassedData=$this->semesterBasedResultsService->which_chance_student_passed_course($studentScoresArray[$index]['chance_1'],$studentScoresArray[$index]['chance_2'],$studentScoresArray[$index]['chance_3'],$studentScoresArray[$index]['chance_4'],$MIN_SCORE_FOR_PASSED_EXAM);

                $student_id=$studentScore->student->id;
                $subject_id=$studentScore->course->subject->id;
                $score_id=$studentScore->id;
                $education_year=$studentScore->course->year;
                $semester=$studentScore->course->semester;
                
                $chance_one=$studentScoresArray[$index]['chance_1'];
                $chance_two=$studentScoresArray[$index]['chance_2'];
                $chance_three=$studentScoresArray[$index]['chance_3'];
                $chance_four=$studentScoresArray[$index]['chance_4'];
                $success_score=$coursePassedData['success_score'];
                $success_chance=$coursePassedData['success_chance'];

                \DB::transaction(function () use ($student_id,$subject_id,$education_year, $semester, $course_id, $score_id, $chance_one , $chance_two ,$chance_three ,$chance_four ,$success_score ,$success_chance){

                    $results=StudentSemesterScore::addSubjectScore($student_id,$subject_id,$education_year, $semester, $course_id, $score_id, $chance_one , $chance_two ,$chance_three ,$chance_four ,$success_score ,$success_chance);

                    $course=Course::where('id',$course_id)->update(['course_status_id' => 2]);

                    $scores=Score::where('id',$score_id)->update(['course_status_id' => 2]);
                    
                });
            }
            else
            {
                $studentScoresArray[$index]['passed']=0;
            }

            $index++;

        }
       

        return view('course.scores.ajax_students', [ //course.attendance.list
            'title' => trans('general.attendance'),
            'description' => trans('general.final_approved'),
            'course' => $course,
            'studentScoresArray' =>  $studentScoresArray,
            'systemVariable' => $systemVariable,
            'MAX_MIDTERM_SCORE' => $MAX_MIDTERM_SCORE->user_value,
            'MAX_HOMEWORK_SCORE' => $MAX_HOMEWORK_SCORE->user_value,
            'MAX_FINAL_SCORE' => $MAX_FINAL_SCORE->user_value,
            'MAX_CLASSWORK_SCORE' => $MAX_CLASSWORK_SCORE->user_value,
            'MIN_PRESENT_PER_SUBJECT' => $MIN_PRESENT_PER_SUBJECT->user_value,
            'NUMBWR_OF_SESSIONS_PER_SEMESTER' => $systemVariable->user_value,
            'MIN_SCORE_FOR_PASSED_EXAM' => $MIN_SCORE_FOR_PASSED_EXAM,
            
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : []
        ]);
    }

    public function chance3_approved(Course $course)
    {
        $course_id=$course->id;
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

       
        $course->loadStudentsAndScoresAndSemesterDeprived();

        $parameters = $this->parameters($course, trans('general.attendance'),  trans('general.chance3_final_approved'));
        $value = ['scores' => $scores];
        $parameters = $parameters +  $value;
       
        return view('course.scores.chance3-approved', $parameters);
    }

    public function final_approved_chance3_insert_scores(Request $request, $course)
    {
        $systemVariable=SystemVariable::where('name','NUMBWR_OF_SESSIONS_PER_SEMESTER')->first();

        $MAX_MIDTERM_SCORE=SystemVariable::where('name','MAX_MIDTERM_SCORE')->first();
        $MAX_HOMEWORK_SCORE=SystemVariable::where('name','MAX_HOMEWORK_SCORE')->first();
        $MAX_FINAL_SCORE=SystemVariable::where('name','MAX_FINAL_SCORE')->first();
        $MAX_CLASSWORK_SCORE=SystemVariable::where('name','MAX_CLASSWORK_SCORE')->first();
        $MIN_PRESENT_PER_SUBJECT=SystemVariable::where('name','MIN_PRESENT_PER_SUBJECT')->first();
        

        $course->loadStudentsAndScoresAndSemesterDeprived();
       
        $studentScoresArray=array();
        $index=0;

        $course_id=$course->id;
        $MIN_SCORE_FOR_PASSED_EXAM=$min_score=$this->MIN_SCORE_FOR_PASSED_EXAM->user_value;

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
       

        foreach($scores as $studentScore)
        {
            $studentScoresArray[$index]['score_id']=$studentScore->id;
            $studentScoresArray[$index]['full_name']=$studentScore->student->full_name;
            $studentScoresArray[$index]['form_no']=$studentScore->student->form_no;
            $studentScoresArray[$index]['father_name']=$studentScore->student->father_name;
            $studentScoresArray[$index]['kankor_year']=$studentScore->student->kankor_year;

            $final_score=(isset($studentScore->final) ? $studentScore->final : null); //final exam score
            $present=(isset($studentScore->present) ? $studentScore->present : 0); //present
            $absent=(isset($studentScore->absent) ? $studentScore->absent : 0); //absent
            $absent_exam=(isset($studentScore->absent_exam) ? $studentScore->absent_exam : null); //absent_exam
            $excuse_exam=(isset($studentScore->excuse_exam) ? $studentScore->excuse_exam : null); //excuse_exam
            $deprived=(isset($studentScore->deprived) ? $studentScore->deprived : null); //deprived

            $studentScoresArray[$index]['chance_1']=
            (isset($studentScore->total) ? $studentScore->total : null); //chance 1
            $studentScoresArray[$index]['chance_2']= 
            (isset($studentScore->chance_two) ? $studentScore->chance_two : null);//chance 2
            $studentScoresArray[$index]['chance_3']= 
            (isset($studentScore->chance_three) ? $studentScore->chance_three : null);//chance 3
            $studentScoresArray[$index]['chance_4']= null;//chance 4

            $student_deprived=is_this_student_deprived_from_exam($present,$absent,$deprived);
            $studentScoresArray[$index]['deprived']=$student_deprived;

            $student_absent=is_this_student_absent_in_final_exam($final_score,$present,$absent,$absent_exam,$excuse_exam,$deprived);
            $studentScoresArray[$index]['absent_exam']=$student_absent;

            $studentScoresArray[$index]['excuse_exam']=$studentScore->excuse_exam;

            $passed_course=is_this_student_passed_this_course($studentScoresArray[$index]['chance_1'],$studentScoresArray[$index]['chance_2'],$studentScoresArray[$index]['chance_3'],$studentScoresArray[$index]['chance_4'],$MIN_SCORE_FOR_PASSED_EXAM);

            if($passed_course)
            {
                $studentScoresArray[$index]['passed']=1;
                

                /* data only inserted into student semester results when course was passed by chance 1 */ 
                $coursePassedData=$this->semesterBasedResultsService->which_chance_student_passed_course($studentScoresArray[$index]['chance_1'],$studentScoresArray[$index]['chance_2'],$studentScoresArray[$index]['chance_3'],$studentScoresArray[$index]['chance_4'],$MIN_SCORE_FOR_PASSED_EXAM);

                $student_id=$studentScore->student->id;
                $subject_id=$studentScore->course->subject->id;
                $score_id=$studentScore->id;
                $education_year=$studentScore->course->year;
                $semester=$studentScore->course->semester;
                
                $chance_one=$studentScoresArray[$index]['chance_1'];
                $chance_two=$studentScoresArray[$index]['chance_2'];
                $chance_three=$studentScoresArray[$index]['chance_3'];
                $chance_four=$studentScoresArray[$index]['chance_4'];
                $success_score=$coursePassedData['success_score'];
                $success_chance=$coursePassedData['success_chance'];

                \DB::transaction(function () use ($student_id,$subject_id,$education_year, $semester, $course_id, $score_id, $chance_one , $chance_two ,$chance_three ,$chance_four ,$success_score ,$success_chance){

                    $results=StudentSemesterScore::addSubjectScore($student_id,$subject_id,$education_year, $semester, $course_id, $score_id, $chance_one , $chance_two ,$chance_three ,$chance_four ,$success_score ,$success_chance);

                    $course=Course::where('id',$course_id)->update(['course_status_id' => 3]);

                    $scores=Score::where('id',$score_id)->update(['course_status_id' => 3]);
                    
                });
            }
            else
            {
                $studentScoresArray[$index]['passed']=0;
            }

            $index++;

        }
       

        return view('course.scores.ajax_students', [ //course.attendance.list
            'title' => trans('general.attendance'),
            'description' => trans('general.final_approved'),
            'course' => $course,
            'studentScoresArray' =>  $studentScoresArray,
            'systemVariable' => $systemVariable,
            'MAX_MIDTERM_SCORE' => $MAX_MIDTERM_SCORE->user_value,
            'MAX_HOMEWORK_SCORE' => $MAX_HOMEWORK_SCORE->user_value,
            'MAX_FINAL_SCORE' => $MAX_FINAL_SCORE->user_value,
            'MAX_CLASSWORK_SCORE' => $MAX_CLASSWORK_SCORE->user_value,
            'MIN_PRESENT_PER_SUBJECT' => $MIN_PRESENT_PER_SUBJECT->user_value,
            'NUMBWR_OF_SESSIONS_PER_SEMESTER' => $systemVariable->user_value,
            'MIN_SCORE_FOR_PASSED_EXAM' => $MIN_SCORE_FOR_PASSED_EXAM,
            
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : []
        ]);
    }

    public function chance4_approved(Course $course)
    {
        $course_id=$course->id;
        $min_score=$this->MIN_SCORE_FOR_PASSED_EXAM->user_value;
        $scores=Score::with('course')->with('student')
        ->where('course_id',$course->id)
        ->where('chance_three','<',$min_score)
        // ->where('passed',0)
        // ->whereNull('chance_two')
        ->get();
        $course->loadStudentsAndScoresAndSemesterDeprived();

        $parameters = $this->parameters($course, trans('general.attendance'),  trans('general.chance4_final_approved'));
        $value = ['scores' => $scores];
        $parameters = $parameters +  $value;
       
        return view('course.scores.chance4-approved', $parameters);
    }

    public function final_approved_chance4_insert_scores(Request $request, $course)
    {
       
        $systemVariable=SystemVariable::where('name','NUMBWR_OF_SESSIONS_PER_SEMESTER')->first();

        $MAX_MIDTERM_SCORE=SystemVariable::where('name','MAX_MIDTERM_SCORE')->first();
        $MAX_HOMEWORK_SCORE=SystemVariable::where('name','MAX_HOMEWORK_SCORE')->first();
        $MAX_FINAL_SCORE=SystemVariable::where('name','MAX_FINAL_SCORE')->first();
        $MAX_CLASSWORK_SCORE=SystemVariable::where('name','MAX_CLASSWORK_SCORE')->first();
        $MIN_PRESENT_PER_SUBJECT=SystemVariable::where('name','MIN_PRESENT_PER_SUBJECT')->first();
        

        $course->loadStudentsAndScoresAndSemesterDeprived();
       
        $studentScoresArray=array();
        $index=0;

        $course_id=$course->id;
        $MIN_SCORE_FOR_PASSED_EXAM=$min_score=$this->MIN_SCORE_FOR_PASSED_EXAM->user_value;

        $scores=Score::with('course')->with('student')
        ->where('course_id',$course->id)
        ->where('chance_three','<',$min_score)
        // ->where('passed',0)
        // ->whereNull('chance_two')
        ->get();
       

        foreach($scores as $studentScore)
        {
            $studentScoresArray[$index]['score_id']=$studentScore->id;
            $studentScoresArray[$index]['full_name']=$studentScore->student->full_name;
            $studentScoresArray[$index]['form_no']=$studentScore->student->form_no;
            $studentScoresArray[$index]['father_name']=$studentScore->student->father_name;
            $studentScoresArray[$index]['kankor_year']=$studentScore->student->kankor_year;

            $final_score=(isset($studentScore->final) ? $studentScore->final : null); //final exam score
            $present=(isset($studentScore->present) ? $studentScore->present : 0); //present
            $absent=(isset($studentScore->absent) ? $studentScore->absent : 0); //absent
            $absent_exam=(isset($studentScore->absent_exam) ? $studentScore->absent_exam : null); //absent_exam
            $excuse_exam=(isset($studentScore->excuse_exam) ? $studentScore->excuse_exam : null); //excuse_exam
            $deprived=(isset($studentScore->deprived) ? $studentScore->deprived : null); //deprived

            $studentScoresArray[$index]['chance_1']=
            (isset($studentScore->total) ? $studentScore->total : null); //chance 1
            $studentScoresArray[$index]['chance_2']= 
            (isset($studentScore->chance_two) ? $studentScore->chance_two : null);//chance 2
            $studentScoresArray[$index]['chance_3']= 
            (isset($studentScore->chance_three) ? $studentScore->chance_three : null);//chance 3
            $studentScoresArray[$index]['chance_4']=  
            (isset($studentScore->chance_four) ? $studentScore->chance_four : null);//chance 4

            $student_deprived=is_this_student_deprived_from_exam($present,$absent,$deprived);
            $studentScoresArray[$index]['deprived']=$student_deprived;

            $student_absent=is_this_student_absent_in_final_exam($final_score,$present,$absent,$absent_exam,$excuse_exam,$deprived);
            $studentScoresArray[$index]['absent_exam']=$student_absent;

            $studentScoresArray[$index]['excuse_exam']=$studentScore->excuse_exam;

            $passed_course=is_this_student_passed_this_course($studentScoresArray[$index]['chance_1'],$studentScoresArray[$index]['chance_2'],$studentScoresArray[$index]['chance_3'],$studentScoresArray[$index]['chance_4'],$MIN_SCORE_FOR_PASSED_EXAM);

            if($passed_course)
            {
                $studentScoresArray[$index]['passed']=1;
                

                /* data only inserted into student semester results when course was passed by chance 1 */ 
                $coursePassedData=$this->semesterBasedResultsService->which_chance_student_passed_course($studentScoresArray[$index]['chance_1'],$studentScoresArray[$index]['chance_2'],$studentScoresArray[$index]['chance_3'],$studentScoresArray[$index]['chance_4'],$MIN_SCORE_FOR_PASSED_EXAM);

                $student_id=$studentScore->student->id;
                $subject_id=$studentScore->course->subject->id;
                $score_id=$studentScore->id;
                $education_year=$studentScore->course->year;
                $semester=$studentScore->course->semester;
                
                $chance_one=$studentScoresArray[$index]['chance_1'];
                $chance_two=$studentScoresArray[$index]['chance_2'];
                $chance_three=$studentScoresArray[$index]['chance_3'];
                $chance_four=$studentScoresArray[$index]['chance_4'];
                $success_score=$coursePassedData['success_score'];
                $success_chance=$coursePassedData['success_chance'];

                \DB::transaction(function () use ($student_id,$subject_id,$education_year, $semester, $course_id, $score_id, $chance_one , $chance_two ,$chance_three ,$chance_four ,$success_score ,$success_chance){

                    $results=StudentSemesterScore::addSubjectScore($student_id,$subject_id,$education_year, $semester, $course_id, $score_id, $chance_one , $chance_two ,$chance_three ,$chance_four ,$success_score ,$success_chance);

                    $course=Course::where('id',$course_id)->update(['course_status_id' => 4]);

                    $scores=Score::where('id',$score_id)->update(['course_status_id' => 4]);
                    
                });
            }
            else
            {
                $studentScoresArray[$index]['passed']=0;
            }

            $index++;

        }
       

        return view('course.scores.ajax_students', [ //course.attendance.list
            'title' => trans('general.attendance'),
            'description' => trans('general.final_approved'),
            'course' => $course,
            'studentScoresArray' =>  $studentScoresArray,
            'systemVariable' => $systemVariable,
            'MAX_MIDTERM_SCORE' => $MAX_MIDTERM_SCORE->user_value,
            'MAX_HOMEWORK_SCORE' => $MAX_HOMEWORK_SCORE->user_value,
            'MAX_FINAL_SCORE' => $MAX_FINAL_SCORE->user_value,
            'MAX_CLASSWORK_SCORE' => $MAX_CLASSWORK_SCORE->user_value,
            'MIN_PRESENT_PER_SUBJECT' => $MIN_PRESENT_PER_SUBJECT->user_value,
            'NUMBWR_OF_SESSIONS_PER_SEMESTER' => $systemVariable->user_value,
            'MIN_SCORE_FOR_PASSED_EXAM' => $MIN_SCORE_FOR_PASSED_EXAM,
            
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : []
        ]);
    }

    public function final_approved_show(Course $course)
    {
       $course_id=$course->id;
        //  dd($course_id);
        $systemVariable=SystemVariable::where('name','NUMBWR_OF_SESSIONS_PER_SEMESTER')->first();

        $MAX_MIDTERM_SCORE=SystemVariable::where('name','MAX_MIDTERM_SCORE')->first();
        $MAX_HOMEWORK_SCORE=SystemVariable::where('name','MAX_HOMEWORK_SCORE')->first();
        $MAX_FINAL_SCORE=SystemVariable::where('name','MAX_FINAL_SCORE')->first();
        $MAX_CLASSWORK_SCORE=SystemVariable::where('name','MAX_CLASSWORK_SCORE')->first();
        $MIN_PRESENT_PER_SUBJECT=SystemVariable::where('name','MIN_PRESENT_PER_SUBJECT')->first();
        $MIN_SCORE_FOR_PASSED_EXAM=SystemVariable::where('name','MIN_SCORE_FOR_PASSED_EXAM')->first();

        // $course->loadStudentsAndScoresAndSemesterDeprived();
        // $min_score=$MIN_SCORE_FOR_PASSED_EXAM->user_value;
        // $scores=Score::with('course')->with('student')
        // ->where('course_id',$course_id)
        // ->orderBy('student_id')
        // ->get();
        $number_of_credits=(int)$course->subject->credits;

        return view('course.scores.final-approved-show', [ //course.attendance.list
            'title' => trans('general.attendance'),
            'description' => trans('general.final_approved'),
            'course' => $course,
            
            'systemVariable' => $systemVariable,
            'MAX_MIDTERM_SCORE' => $MAX_MIDTERM_SCORE->user_value,
            'MAX_HOMEWORK_SCORE' => $MAX_HOMEWORK_SCORE->user_value,
            'MAX_FINAL_SCORE' => $MAX_FINAL_SCORE->user_value,
            'MAX_CLASSWORK_SCORE' => $MAX_CLASSWORK_SCORE->user_value,
            'MIN_PRESENT_PER_SUBJECT' => $MIN_PRESENT_PER_SUBJECT->user_value,
            'NUMBWR_OF_SESSIONS_PER_SEMESTER' => $systemVariable->user_value,
            'MIN_SCORE_FOR_PASSED_EXAM' => $MIN_SCORE_FOR_PASSED_EXAM->user_value,
            'number_of_credits' => $number_of_credits,
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : []
        ]);
    }

    public function final_approved_insert_scores(Request $request, $course)
    {
        
       $course_id=$request->input('course_id');
         
        $systemVariable=SystemVariable::where('name','NUMBWR_OF_SESSIONS_PER_SEMESTER')->first();

        $MAX_MIDTERM_SCORE=SystemVariable::where('name','MAX_MIDTERM_SCORE')->first();
        $MAX_HOMEWORK_SCORE=SystemVariable::where('name','MAX_HOMEWORK_SCORE')->first();
        $MAX_FINAL_SCORE=SystemVariable::where('name','MAX_FINAL_SCORE')->first();
        $MAX_CLASSWORK_SCORE=SystemVariable::where('name','MAX_CLASSWORK_SCORE')->first();
        $MIN_PRESENT_PER_SUBJECT=SystemVariable::where('name','MIN_PRESENT_PER_SUBJECT')->first();
        $min_score=SystemVariable::where('name','MIN_SCORE_FOR_PASSED_EXAM')->first();

        $course->loadStudentsAndScoresAndSemesterDeprived();
        $MIN_SCORE_FOR_PASSED_EXAM=$min_score->user_value;
        $studentScoresArray=array();
        $index=0;
        $scores=Score::with('course')->with('student')
        ->where('course_id',$course_id)
        ->orderBy('student_id')
        ->get();

        foreach($scores as $studentScore)
        {
            $studentScoresArray[$index]['score_id']=$studentScore->id;
            $studentScoresArray[$index]['full_name']=$studentScore->student->full_name;
            $studentScoresArray[$index]['form_no']=$studentScore->student->form_no;
            $studentScoresArray[$index]['father_name']=$studentScore->student->father_name;
            $studentScoresArray[$index]['kankor_year']=$studentScore->student->kankor_year;

            $final_score=(isset($studentScore->final) ? $studentScore->final : null); //final exam score
            $present=(isset($studentScore->present) ? $studentScore->present : 0); //present
            $absent=(isset($studentScore->absent) ? $studentScore->absent : 0); //absent
            $absent_exam=(isset($studentScore->absent_exam) ? $studentScore->absent_exam : null); //absent_exam
            $excuse_exam=(isset($studentScore->excuse_exam) ? $studentScore->excuse_exam : null); //excuse_exam
            $deprived=(isset($studentScore->deprived) ? $studentScore->deprived : null); //deprived

            $studentScoresArray[$index]['chance_1']=
            (isset($studentScore->total) ? $studentScore->total : null); //chance 1
            $studentScoresArray[$index]['chance_2']=
            (isset($studentScore->chance_two) ? $studentScore->chance_two : null);//chance 2
            $studentScoresArray[$index]['chance_3']=
            (isset($studentScore->chance_three) ? $studentScore->chance_three : null);//chance 3
            $studentScoresArray[$index]['chance_4']=
            (isset($studentScore->chance_four) ? $studentScore->chance_four : null);//chance 4

            $student_deprived=is_this_student_deprived_from_exam($present,$absent,$deprived);
            $studentScoresArray[$index]['deprived']=$student_deprived;

            $student_absent=is_this_student_absent_in_final_exam($final_score,$present,$absent,$absent_exam,$excuse_exam,$deprived);
            $studentScoresArray[$index]['absent_exam']=$student_absent;

            $studentScoresArray[$index]['excuse_exam']=$studentScore->excuse_exam;

            $passed_course=is_this_student_passed_this_course($studentScoresArray[$index]['chance_1'],$studentScoresArray[$index]['chance_2'],$studentScoresArray[$index]['chance_3'],$studentScoresArray[$index]['chance_4'],$MIN_SCORE_FOR_PASSED_EXAM);

            if($passed_course)
            {
                $studentScoresArray[$index]['passed']=1;
                

                /* data only inserted into student semester results when course was passed */ 
                $coursePassedData=$this->semesterBasedResultsService->which_chance_student_passed_course($studentScoresArray[$index]['chance_1'],$studentScoresArray[$index]['chance_2'],$studentScoresArray[$index]['chance_3'],$studentScoresArray[$index]['chance_4'],$MIN_SCORE_FOR_PASSED_EXAM);

                $student_id=$studentScore->student->id;
                $subject_id=$studentScore->course->subject->id;
                $score_id=$studentScore->id;
                $education_year=$studentScore->course->year;
                $semester=$studentScore->course->semester;
                
                $chance_one=$studentScoresArray[$index]['chance_1'];
                $chance_two=$studentScoresArray[$index]['chance_2'];
                $chance_three=$studentScoresArray[$index]['chance_3'];
                $chance_four=$studentScoresArray[$index]['chance_4'];
                $success_score=$coursePassedData['success_score'];
                $success_chance=$coursePassedData['success_chance'];

                \DB::transaction(function () use ($student_id,$subject_id,$education_year, $semester, $course_id, $score_id, $chance_one , $chance_two ,$chance_three ,$chance_four ,$success_score ,$success_chance){

                    $results=StudentSemesterScore::addSubjectScore($student_id,$subject_id,$education_year, $semester, $course_id, $score_id, $chance_one , $chance_two ,$chance_three ,$chance_four ,$success_score ,$success_chance);

                    $course=Course::where('id',$course_id)->update(['final_approved' => 1]);

                    $scores=Score::where('id',$score_id)->update(['final_approved' => 1]);
                    
                });
            }
            else
            {
                $studentScoresArray[$index]['passed']=0;
            }

            $index++;

        }
       

        return view('course.scores.ajax_students', [ //course.attendance.list
            'title' => trans('general.attendance'),
            'description' => trans('general.final_approved'),
            'course' => $course,
            'studentScoresArray' =>  $studentScoresArray,
            'systemVariable' => $systemVariable,
            'MAX_MIDTERM_SCORE' => $MAX_MIDTERM_SCORE->user_value,
            'MAX_HOMEWORK_SCORE' => $MAX_HOMEWORK_SCORE->user_value,
            'MAX_FINAL_SCORE' => $MAX_FINAL_SCORE->user_value,
            'MAX_CLASSWORK_SCORE' => $MAX_CLASSWORK_SCORE->user_value,
            'MIN_PRESENT_PER_SUBJECT' => $MIN_PRESENT_PER_SUBJECT->user_value,
            'NUMBWR_OF_SESSIONS_PER_SEMESTER' => $systemVariable->user_value,
            'MIN_SCORE_FOR_PASSED_EXAM' => $MIN_SCORE_FOR_PASSED_EXAM,
            
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : []
        ]);
    }

    public function update_present_in_score_table(Request $request, $course)
    {
       $course_id=$request->input('course_id');
       $present_value=$request->input('present_value');

       if($course_id && $present_value > 0 )
       {
        $scores=Score::where('course_id',$course_id)
        ->where('present','<>',$present_value)
        ->update(['present' => $present_value]);
        return $scores." ".trans('general.rows_updated') ;
       }
       return 0 ." ".trans('general.rows_updated');
         
    }

    public function import_chance1_from_excel(Course $course)
    {
        dd($course);
    }

    public function score_chance1_from_excel(Request $request, $course)
    {
        dd($course);
    }

}
