<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Score;
use App\Models\StudentResult;
use App\Models\StudentSemesterScore;
use App\Models\StudentStatus;
use App\Models\University;
use PDF;

class StudentAllDetailsController extends Controller
{
    public function index($student)
    { 
        $coursesList=Course::select(
            'courses.id',
            'courses.code',
            'courses.year',
            'courses.half_year',
            'courses.semester',
            'courses.subject_id',
            'courses.teacher_id',
            'courses.university_id',
            'courses.department_id',
            'courses.active',
            'courses.approve_by_teacher',
            'courses.course_status_id',
            'courses.deleted_at'
        )
        ->leftjoin('course_student','courses.id', '=', 'course_student.course_id')
        ->with('teacher')
        ->with('subject')
        ->with('groups')
        ->where('course_student.student_id',$student->id)
        ->orderBy('year')
        ->orderBy('semester')
        ->orderBy('courses.id')
        ->get();
       

        $scores=Score::where('scores.student_id',$student->id)
        ->with('course')
        ->with('subject')
        ->with('course_status')
        ->orderBy('semester')
        ->get();

        $studentResults = StudentResult::where('student_id',$student->id)
        ->orderBy('education_year')
        ->orderBy('semester')
        ->get();

        $studentSemesterScores = StudentSemesterScore::where('student_id',$student->id)
        ->orderBy('education_year')
        ->orderBy('semester')
        ->get();

        return view('students.all-details', [
            'title' => trans('general.students'),
            'description' => trans('general.all_details_of_student'),
            'student' => $student,
            'universities' => University::pluck('name', 'id'),
            'statuses' => StudentStatus::whereIn('id', [1, 2])->pluck('title', 'id'),
            'grades' => Grade::pluck('name', 'id'),
            'scores' => $scores,
            'studentResults' => $studentResults,
            'studentSemesterScores' => $studentSemesterScores,
            'coursesList' => $coursesList,
        ]);
    }

}
