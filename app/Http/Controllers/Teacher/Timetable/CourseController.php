<?php

namespace App\Http\Controllers\Teacher\Timetable;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Curriculum;
use App\Models\ScheduleItem;
use App\Models\Semester;
use Illuminate\Http\Request;

class CourseController extends Controller
{

	public function __invoke( Request $request )
	{
		$teacher = auth()->user();
		if( $request->year) {
			$selectedYear = $request->year;
		} else {
			$selectedYear = 0;
		}
		$coursesList = array(); 
		$currentSemesterCoursesList = array(); 
		$index = 0;
		$yearAndHalfYears = Course::select('year','half_year')
		->distinct()
		->where('teacher_id',$teacher->id)
		->orderBy('year', 'DESC')
		->orderBy('half_year', 'ASC')
		->groupBy('year','half_year')
		->get();

		$education_years = $yearAndHalfYears->pluck('year','year')->unique();
		
		$coursesByTeacher = Course::with('subject:id,title,credits')
		->select('courses.*')
		->where('teacher_id',$teacher->id)
		->orderBy('year', 'DESC')
		->orderBy('half_year', 'ASC')
		->get();

		if($selectedYear > 0)
		{
			$coursesByTeacher = $coursesByTeacher->where('year',$selectedYear);
			$yearAndHalfYears = $yearAndHalfYears->where('year',$selectedYear);
		}
		

		foreach ($yearAndHalfYears as $yearAndHalfYear) 
		{
			$coursesList['year'][$index] = $yearAndHalfYear->year;
			$coursesList['half_year'][$index] = $yearAndHalfYear->half_year;
			$totalCredits = 0;
			$coursesListByTeacher = $coursesByTeacher->where('year',$yearAndHalfYear->year)
			->where('half_year',$yearAndHalfYear->half_year);
			$coursesList['count'][$index] = $coursesListByTeacher->count();

			foreach($coursesListByTeacher as $courseListByTeacher)
			{
				$totalCredits += $courseListByTeacher->subject->credits;
			}
			$coursesList['totalCredits'][$index] = $totalCredits;
			$index++;
		}

		$coursesListByYearAndSemester = array(); 
		$i = 0; //index of day. 0 means without day.1 means saturday.
		$j = 0; //amount of courses for each day
		$index = 0;
		$year = $coursesList['year'][0];
		$half_year =$coursesList['half_year'][0];
		for($i = 0; $i < 7; $i++)
		{
			$coursesListByYearAndSemester[$i][0] = null;
		}

		if($selectedYear == 0) 
		{
			$coursesByTeacher = Course::with('subject:id,title,credits')
			->with('courseTimes')
			->with('department')
			->with('groupsName')
			->select('courses.*')
			->where('teacher_id',$teacher->id)
			->where('year',$year)
			->where('half_year',$half_year)
			->orderBy('semester')
			->get();
	
			foreach ($coursesByTeacher as $course) {
				if($course->courseTimes->count() >= 1)
				{
					$j = 0;
					foreach($course->courseTimes as $courseTime)
					{
						$index = $courseTime->day_id;
						$coursesListByYearAndSemester[$index][$j] = $course;
						$j++;
					}
					$j = 0;
				}
				else
				{
					$index = 0;
					$coursesListByYearAndSemester[$index][$j] = $course;
					$j++;
				}
			}
		}
		

		
		return view('teacher.timetable.course', [ 
			'teacher' => $teacher,  
			'coursesList' => $coursesList,
			'coursesListByYearAndSemester' => $coursesListByYearAndSemester,
			'title' => trans('general.timetable'),
			'description' => trans('general.courses'),
			'education_years' => $education_years,
			'year' => $year,
			'half_year' => $half_year,
			'selectedYear' => $selectedYear
		]);    

	}
	public function getListOfCoursesByYearAndSemester(Request $request)
    { 
		$year = $request->year;
		$half_year = $request->half_year;
		$teacher = auth()->user();
		$coursesList = array(); 
		$i = 0; //index of day. 0 means without day.1 means saturday.
		$j = 0; //amount of courses for each day
		$index = 0;

		for($i = 0; $i < 7; $i++)
		{
			$coursesList[$i][0] = null;
		}

		$coursesByTeacher = Course::with('subject:id,title,credits')
		->with('courseTimes')
		->with('department')
		->with('groupsName')
		->select('courses.*')
		->where('teacher_id',$teacher->id)
		->where('year',$year)
		->where('half_year',$half_year)
		->orderBy('semester')
		->get();

		foreach ($coursesByTeacher as $course) {
			if($course->courseTimes->count() >= 1)
			{
				$j = 0;
				foreach($course->courseTimes as $courseTime)
				{
					$index = $courseTime->day_id;
					$coursesList[$index][$j] = $course;
					$j++;
				}
				$j = 0;
			}
			else
			{
				$index = 0;
				$coursesList[$index][$j] = $course;
				$j++;
			}
		}

		return view('teacher.timetable.ajax-by-year', [
            'title' => trans('general.statistics'),
            'description' => trans('general.student_statistics'),
            'year' => $year,
			'half_year' => $half_year,
			'coursesList' => $coursesList
        ]);
	}

	function exams(Request $request)
	{	
		$teacher = auth()->user();
		

		$semesterId = $request->has('semester') ? $request->get('semester') : defaultSemesterId();		

		$courses = $teacher->courses()->withoutGlobalScope('department')->where('semester_id', $semesterId)->get();
		
		return view('teacher.examTimetable', [	
			'semesters' => Semester::orderBy('id', 'desc')->take(5)->get(),
			'courses' => $courses,			
			'semesterId' => $semesterId
		]);	
	}

}