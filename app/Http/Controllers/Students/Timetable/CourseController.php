<?php

namespace App\Http\Controllers\Students\Timetable;

use App\Http\Controllers\Controller;
use App\Models\CourseTime;
use App\Models\Curriculum;
use App\Models\ScheduleItem;
use App\Models\Semester;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __invoke(Request $request)
    {
    	$student = auth('student')->user();
        $student_semester = $student->semester;
        $semesterList = $student->courses()->groupBy('semester')->latest('semester')->distinct()->first(['semester']);
        $semester = $semesterList->semester;
       
        if( $semester)
        {
            $student_semester = $semester;
        }

        $courses = $student->courses()->where('semester' , $student_semester)->get();
	    $courseTimes = CourseTime::with('course')->whereIn('course_id', $courses->pluck('id'))->get(); 	 
        
        return view('student.timetable.course', [
        	'student' => $student,        	
        	'unscheduledCourses' => $courses,
			'courseTimes' => $courseTimes,
			'title' => trans('general.timetable'),
			'description' => trans('general.courses'),
        ]);    
    }
}