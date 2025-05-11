<?php

namespace App\Http\Controllers\Students\AuthStudent;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use App\Models\StudentStatus;
use App\Models\University;
use App\Services\StudentService;


class StudentController extends Controller
{
    protected $studentId;
    protected $studentService;

    public function __construct(StudentService $studentService)
    { 
        $this->studentService = $studentService;
    }

    public function index()
    {
        
        $this->studentId = get_student_id();
        $student = Student::find($this->studentId);
        return view('students.auth_students.profile',
        [
            'title' => trans('general.students'),
            'description' => trans('general.profile'),
            'student' => $student,
            'universities' => University::pluck('name', 'id'),
            'statuses' => StudentStatus::whereIn('id', [1, 2])->pluck('title', 'id'),
            'grades' => Grade::pluck('name', 'id'),
        ]);

    }

    public function group()
    {
        $this->studentId = get_student_id();
        $studentCurrentGroup = $this->studentService->current_group($this->studentId);
        $studentGroupsHistory = $this->studentService->group_history($this->studentId);
        
        return view('students.auth_students.group',
        [
            'title' => trans('general.students'),
            'description' => trans('general.group_specifications'),
            'studentCurrentGroup' => $studentCurrentGroup,
            'studentGroupsHistory' => $studentGroupsHistory,
        ]);
    }

    public function leave()
    {
        $this->studentId = get_student_id();
        $studentLeave = $this->studentService->leave($this->studentId);
       
        
        return view('students.auth_students.leave',
        [
            'title' => trans('general.students'),
            'description' => trans('general.leave_specifications'),
            'studentLeave' => $studentLeave,
        ]);
    }

    public function dropout()
    {
        $this->studentId = get_student_id();
        $studentDropout = $this->studentService->dropout($this->studentId);
       
        
        return view('students.auth_students.dropout',
        [
            'title' => trans('general.students'),
            'description' => trans('general.leave_specifications'),
            'studentDropout' => $studentDropout,
        ]);
    }

    public function transfer()
    {
        $this->studentId = get_student_id();
        $studentTransfer = $this->studentService->transfer($this->studentId);
       
        
        return view('students.auth_students.transfer',
        [
            'title' => trans('general.students'),
            'description' => trans('general.transfer_specifications'),
            'studentTransfer' => $studentTransfer,
        ]);
    }

    public function information()
    {
        $this->studentId = get_student_id();
        $student = $this->studentService->information($this->studentId);
        
        return view('students.auth_students.information',
        [
            'title' => trans('general.student'),
            'description' => trans('general.information'),
            'student' => $student,
        ]);
    }

    public function coursesList()
    {
        $this->studentId = get_student_id();
        $studentCoursesList = $this->studentService->coursesList($this->studentId);
      
        return view('students.auth_students.courses-list',
        [
            'title' => trans('general.student'),
            'description' => trans('general.courses_list'),
            'studentCoursesList' => $studentCoursesList,
        ]);
    }

    public function scoresList()
    {
        $this->studentId = get_student_id();
        $studentScoresList = $this->studentService->scoresList($this->studentId);
      
        return view('students.auth_students.scores-list',
        [
            'title' => trans('general.student'),
            'description' => trans('general.scores-list'),
            'studentScoresList' => $studentScoresList,
        ]);
    }

    public function finalScoresList()
    {
        $this->studentId = get_student_id();
        $studentData = $this->studentService->finalScoresList($this->studentId);
        $studentScoresList =  $studentData['studentScoresList'];
        $studentResults = $studentData['studentResults'];

        // dd($studentResults);
      
        return view('students.auth_students.final-scores-list',
        [
            'title' => trans('general.student'),
            'description' => trans('general.final-scores-list'),
            'studentScoresList' => $studentScoresList,
            'studentResults' => $studentResults,
        ]);
    }

}
