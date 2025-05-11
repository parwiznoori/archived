<?php
namespace App\Exports;

use App\Models\Student;
use App\Models\LessonWeek;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\SystemVariable;

class CourseStudentsExport implements FromView 
{
    public $course;
    public function __construct($course)
    {
        $this->course = $course;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        $course = $this->course->loadStudentsAndScoresAndSemesterDeprived();
        $numberOfCredits=(int)$course->subject->credits;
        $lessonWeek = LessonWeek::where('education_year',$course->year)
        ->where('half_year',$course->half_year)
        ->where('department_id',$course->department_id)
        ->select('number_of_weeks')
        ->first();
        $system_variables = SystemVariable::select('name','default_value','user_value')->get();
        $NUMBWR_OF_SESSIONS_PER_SEMESTER=$system_variables->where('name','NUMBWR_OF_SESSIONS_PER_SEMESTER')->first();
        $MIN_PRESENT_PER_SUBJECT=$system_variables->where('name','MIN_PRESENT_PER_SUBJECT')->first();
        $teacherFullName = $course->teacher->full_name;

        $session_per_semester=16;
        $sessionPerSemesterDefaultValue = $NUMBWR_OF_SESSIONS_PER_SEMESTER->default_value;
       
        if(isset($NUMBWR_OF_SESSIONS_PER_SEMESTER->user_value))
        {
            $session_per_semester=$NUMBWR_OF_SESSIONS_PER_SEMESTER->user_value;
        }
        else if(isset($NUMBWR_OF_SESSIONS_PER_SEMESTER->default_value))
        {
            $session_per_semester=$NUMBWR_OF_SESSIONS_PER_SEMESTER->default_value;
        }
        else 
        {
            $session_per_semester=16;
        }

        $sessionPerSemester = $session_per_semester;
        $maxPresent=$sessionPerSemester * $numberOfCredits;
        $maxPresentOverAll=$sessionPerSemesterDefaultValue * $numberOfCredits;

        $numberOfCourseWeeks = $lessonWeek ? $lessonWeek->number_of_weeks : $sessionPerSemester;
        $numberOfCourseHours = $numberOfCourseWeeks * $numberOfCredits;
        $legalMaxAbsentHours = ((100- $MIN_PRESENT_PER_SUBJECT->user_value)*($numberOfCourseHours))/100 ;
       
        return view('course.students-export-excel-chance1', [
            'course' => $course,
            'lesson_week' => $lessonWeek,
            'NUMBWR_OF_SESSIONS_PER_SEMESTER' => $NUMBWR_OF_SESSIONS_PER_SEMESTER,
            'legalMaxAbsentHours' => $legalMaxAbsentHours,
            'numberOfCourseHours' => $numberOfCourseHours,
            'teacherFullName' => $teacherFullName,
        ]);
    }
}
