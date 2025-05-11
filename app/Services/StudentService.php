<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\SystemVariable;
use App\Models\Group;
use App\Models\Course;
use App\Models\Score;
use App\Models\Department;
use App\Models\Dropout;
use App\Models\Leave;
use App\Models\Monograph;
use App\Models\Student;
use App\Models\University;
use App\Models\Subject;
use App\Models\Transfer;
use App\Models\StudentSemesterScore;
use App\Models\StudentResult;


class StudentService
{
    public $studentId;
    public $MIN_SCORE_FOR_PASSED_SEMESTER;

    public function __construct($studentId = null)
    {
       $this->studentId = $studentId;
       $this->MIN_SCORE_FOR_PASSED_SEMESTER=SystemVariable::where('name','MIN_SCORE_FOR_PASSED_SEMESTER')->first()->user_value;
    }

    public function studentInformation()
    {
        $student= Student::findOrFail($this->studentId);
        return $student;
    }

    public function find_student_results()
    {
        $studentResultsSemesterCount = StudentResult::where('student_id',$this->studentId)
        ->where('isPassed', 1)                
        ->count();
        return $studentResultsSemesterCount;
    }

    public function find_total_success_credits()
    {
        $studentSuccessCredits = 0;
        $studentScores = StudentSemesterScore::with('subject')
        ->where('student_id',$this->studentId)        
        ->get();

        foreach($studentScores as $score ) {
            $studentSuccessCredits += (int) $score->subject->credits;
        }

        return $studentSuccessCredits;
    }
    
    public function monograph() {
        $monograph= Monograph::where('student_id',$this->studentId)               
        ->first();
        return $monograph;
    }

    public function monographWithTeacher() {
        $monograph=Monograph::with('teacher')
        ->where('student_id',$this->studentId)
        ->first();
        return $monograph;
    }
    /*
    for graduating student : must has threee condithions
    1 : his/her monograph must be registered in system.
    2 : he/she must passed all credits for department.
    3 : he/she must passed all semmesters for department.
    */
    public function is_graduated_this_student( $has_mongraph ,$studentResultsSemesterCount , $studentSuccessCredits ,$min_credits_for_graduated ,$number_of_semesters)
    {
        if( $has_mongraph > 0 && $studentResultsSemesterCount >= $number_of_semesters && $studentSuccessCredits >= $min_credits_for_graduated)
        {
            return true;
        }
        else return false;
    }

    public function current_group($studentId)
    {
        $student = Student::where('id',$studentId)->select('id','form_no','group_id')
        ->with('group')
        ->first();

        $studentCurrentGroup['id'] = $student->group_id ? $student->group->id : '';
        $studentCurrentGroup['name'] = $student->group_id ? $student->group->name : '';
        $studentCurrentGroup['kankor_year'] = $student->group_id ? $student->group->kankor_year : '';
        
        return $studentCurrentGroup;

    }

    public function group_history($studentId)
    {
        $student = Student::where('id',$studentId)->select('id','form_no','group_id')
        ->with('group_history')
        ->first();

        $studentGroupsHistory = array();
        foreach ($student->group_history as $student_group)
        {
            $studentGroupsHistory['id'] = $student_group->id;
            $studentGroupsHistory['name'] = $student_group->name;
            $studentGroupsHistory['kankor_year'] = $student_group->kankor_year;
            $studentGroupsHistory['created_at'] = $student_group->pivot->created_at;
        }
        return $studentGroupsHistory;
    }

    public function leave($studentId)
    {
        $student = Student::where('id',$studentId)->select('id','form_no','group_id')
        ->with('leaves')
        ->first();
        $studentLeaves = array();
        foreach ( $student->leaves as $student_leave)
        {
            $studentLeaves['leave_year'] = $student_leave->leave_year;
            $studentLeaves['semester'] = $student_leave->semester;
            $studentLeaves['semester_type_id'] = $student_leave->semester_type_id;
            $studentLeaves['university'] = $student_leave->university_id ? $student_leave->university->name : '';
            $studentLeaves['department'] = $student_leave->department_id ? $student_leave->department->name : '';
            $studentLeaves['note'] = $student_leave->note;
            $studentLeaves['approved'] = $student_leave->approved;
            $studentLeaves['end_leave'] = $student_leave->end_leave;
        }
        
        return $studentLeaves;
    }

    public function getLeaveYear()
    {
        $student = Leave::where('student_id',$this->studentId)
        ->where('approved',1)
        ->select('leave_year')
        ->first();
        return $student;
    }

    public function dropout($studentId)
    {
        $student = Student::where('id',$studentId)->select('id','form_no','group_id')
        ->with('dropout')
        ->first();
        $studentDropout = array();
        foreach ( $student->dropout as $student_dropout)
        {
            $studentDropout['year'] = $student_dropout->year;
            $studentDropout['semester'] = $student_dropout->semester;
            $studentDropout['university'] = $student_dropout->university_id ? $student_dropout->university->name : '';
            $studentDropout['note'] = $student_dropout->note;
            $studentDropout['approved'] = $student_dropout->approved;
            $studentDropout['removal_dropout'] = $student_dropout->removal_dropout;
            $studentDropout['permanent'] = $student_dropout->permanent;
        }
        
        return $studentDropout;

    }

    public function transfer($studentId)
    {
        $student = Student::where('id',$studentId)->select('id','form_no','group_id')
        ->with('transfer')
        ->first();
        $studentTransfer = array();
        foreach ( $student->transfer as $student_transfer)
        {
            $studentTransfer['education_year'] = $student_transfer->education_year;
            $studentTransfer['semester'] = $student_transfer->semester;
            $studentTransfer['from_university'] = $student_transfer->fromDepartment->university->name;
            $studentTransfer['from_department'] = $student_transfer->fromDepartment->name;
            $studentTransfer['to_university'] = $student_transfer->toDepartment->university->name;
            $studentTransfer['to_department'] =  $student_transfer->toDepartment->name;
            $studentTransfer['note'] = $student_transfer->note;
            $studentTransfer['approved'] = $student_transfer->approved;
            $studentTransfer['exception'] = $student_transfer->exception;
        }
        
        return $studentTransfer;
    }

    public function transferDescribtion()
    {
        $student = Student::where('id',$this->studentId)->select('id','form_no','group_id')
        ->with('transfer')
        ->first();
        $transferDescribtion = '';
        $studentTransfer = array();
        foreach ( $student->transfer as $student_transfer)
        {
            $transferDescribtion .=  __('general.from').' '.$student_transfer->fromDepartment->name .'('.$student_transfer->fromDepartment->university->name.')'. ' '. __('general.to').' '.$student_transfer->toDepartment->name.'('.$student_transfer->toDepartment->university->name.')'.' ['.$student_transfer->education_year.']' ; 
        }
        
        return $transferDescribtion;
    }

    public function information($studentId)
    {
        $student = Student::where('id',$studentId)->select('*')
        ->with('university')
        ->with('department')
        ->first();
        
        return $student;
    }

    public function coursesList($studentId)
    {
        $semesters = array();
        $j=0;
        $semestersList = Course::leftjoin('course_student','courses.id', '=', 'course_student.course_id')
        ->where('course_student.student_id',$studentId)
        ->latest('semester')
        ->distinct()
        ->get(['courses.semester']);

        foreach($semestersList as $semesterList)
        {
            $semesters[$j] = $semesterList->semester;
            $j++;
        }

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
        ->where('course_student.student_id',$studentId)
        ->latest('semester')
        ->orderBy('year')
        ->orderBy('courses.id')
        ->get();
        
        $cList = array();
        $cList = $coursesList->groupBy('semester');


        $studentCoursesList = array();
        $i=0;
        foreach ( $cList as $semester => $courses)
        {
            $i=0;
            foreach($courses as $course)
            {

            
                $groupList = '';
                $g=array();
                $z = 0;
                foreach($course->groups as $group)
                {
                    $g[$z] = $group->id;
                    $z++;
                }
                $groupList = implode(',',$g); 

                $studentCoursesList[$semester][$i]['id'] = $course->id;
                $studentCoursesList[$semester][$i]['code'] = $course->code;
                $studentCoursesList[$semester][$i]['year'] = $course->year;
                $studentCoursesList[$semester][$i]['half_year'] = get_half_year_name($course->half_year);
                $studentCoursesList[$semester][$i]['semester'] = $course->semester;
                $studentCoursesList[$semester][$i]['subject'] =  $course->subject->title;
                $studentCoursesList[$semester][$i]['teacher'] = $course->teacher->full_name;
                $studentCoursesList[$semester][$i]['group'] = $groupList;
                $i++;
            }
        }

        return $studentCoursesList;
    }

    public function scoresList($studentId)
    {
        $scoresList=Score::where('scores.student_id',$studentId)
        ->with('course')
        ->with('subject')
        ->with('course_status')
        ->orderBy('scores.semester')
        ->get();
        
        $cList = array();
        $cList = $scoresList->groupBy('semester');
        
        $studentScoresList = array();
        $i=0;
        foreach ( $cList as $semester => $scores)
        {
            $i=0;
            foreach($scores as $score)
            {
                $status = '';
                $studentScoresList[$semester][$i]['id'] = $score->id;
                $studentScoresList[$semester][$i]['course_id'] = $score->course_id;
                $studentScoresList[$semester][$i]['subject'] = $score->subject->title;
                $studentScoresList[$semester][$i]['credit'] = $score->subject->credits;
                $studentScoresList[$semester][$i]['semester'] = $score->semester;
                $studentScoresList[$semester][$i]['present'] =  $score->present;
                $studentScoresList[$semester][$i]['absent'] = $score->absent;
                $studentScoresList[$semester][$i]['homework'] = $score->homework;
                $studentScoresList[$semester][$i]['classwork'] = $score->classwork;
                $studentScoresList[$semester][$i]['midterm'] = $score->midterm;
                $studentScoresList[$semester][$i]['final'] = $score->final;
                $studentScoresList[$semester][$i]['total'] = $score->total;
                $studentScoresList[$semester][$i]['chance_two'] = $score->chance_two;
                $studentScoresList[$semester][$i]['chance_three'] = $score->chance_three;
                $studentScoresList[$semester][$i]['chance_four'] = $score->chance_four;

                $studentScoresList[$semester][$i]['status'] = get_score_status($score->passed,$score->deprived,$score->absent_exam,$score->excuse_exam);

                $studentScoresList[$semester][$i]['passed'] = $score->passed;
                $studentScoresList[$semester][$i]['deprived'] = $score->deprived;
                $studentScoresList[$semester][$i]['absent_exam'] = $score->absent_exam;
                $studentScoresList[$semester][$i]['excuse_exam'] = $score->excuse_exam;

                $studentScoresList[$semester][$i]['course_status_id'] = $score->course_status_id ? $score->course_status->name : '';
                $studentScoresList[$semester][$i]['final_approved'] = $score->final_approved;
                $i++;
            }
        }

        return $studentScoresList;
    }

    public function finalScoresList($studentId)
    {
        $studentData = array();
        $studentResults = StudentResult::where('student_id',$studentId)
        ->latest('semester')
        ->orderBy('education_year')
        ->get();
       
        $studentResultsList =array();
        foreach($studentResults as  $result)
        {
            $semester = $result->semester;

            $studentResultsList[$semester]['id'] = $result->id;
            $studentResultsList[$semester]['education_year'] = $result->education_year;
            $studentResultsList[$semester]['semester'] = $result->semester;
            $studentResultsList[$semester]['result'] = $result->result;
            $studentResultsList[$semester]['grade'] = $result->grade;
            $studentResultsList[$semester]['isPassed'] = $result->isPassed >=1 ? __('general.yes') : __('general.no') ;
            $studentResultsList[$semester]['increase_semester'] = $result->increase_semester >= 1 ? __('general.yes') : __('general.no') ;
            $studentResultsList[$semester]['semester_credits'] = $result->semester_credits;
            $studentResultsList[$semester]['success_credits'] = $result->success_credits;
        }

        $studentData['studentResults'] = $studentResultsList;

        $scoresList = StudentSemesterScore::where('student_id',$studentId)
        ->with('course')
        ->with('subject')
        ->orderBy('education_year')
        ->latest('semester')
        ->get();

        
        $cList = array();
        $cList = $scoresList->groupBy('semester');
        
        
        $studentScoresList = array();
        $i=0;
        foreach ( $cList as $semester => $scores)
        {
            $i=0;
            foreach($scores as $score)
            {
                $status = '';
                $studentScoresList[$semester][$i]['id'] = $score->id;
                $studentScoresList[$semester][$i]['course_id'] = $score->course_id;
                $studentScoresList[$semester][$i]['score_id'] =  $score->score_id;
                $studentScoresList[$semester][$i]['subject'] = $score->subject->title;
                $studentScoresList[$semester][$i]['credits'] = $score->subject->credits;
                $studentScoresList[$semester][$i]['education_year'] = $score->education_year;
                $studentScoresList[$semester][$i]['semester'] = $score->semester;
                $studentScoresList[$semester][$i]['chance_one'] = $score->chance_one;
                $studentScoresList[$semester][$i]['chance_two'] = $score->chance_two;
                $studentScoresList[$semester][$i]['chance_three'] = $score->chance_three;
                $studentScoresList[$semester][$i]['chance_four'] = $score->chance_four;
                $studentScoresList[$semester][$i]['success_score'] = $score->success_score;
                $studentScoresList[$semester][$i]['success_chance'] = $score->success_chance;
                $studentScoresList[$semester][$i]['scoreAndCridit'] = $score->success_score * $score->subject->credits;
        
                $i++;
            }
        }

        $studentData['studentScoresList'] = $studentScoresList;
        return $studentData;
    }

    public function semesterCount()
    {
        $student = Student::leftJoin('departments', 'departments.id', '=', 'students.department_id')
        ->where('students.id',$this->studentId)
        ->select('departments.number_of_semesters')
        ->first();
        
        return $student;

    }

    public function graduateStudentResults()
    {
        $student = $this->studentInformation();
        $studentData = array();
        $studentScores = array();
        $studentResult = array();
        $educationalYearPerSemesters = array();
        $studentData['semestersCount'] = $semestersCount = $this->semesterCount()->number_of_semesters;

        $totalCredits = 0;
        $totalScores = 0;
        $averageScores=0;
        $result='';
        $maxSubjectsCount=0;
        $totalCreditsAllSemesters=0;
        $totalScoresAllSemesters=0;
        $averageScoresAllSemesters=0;

        $studentData['scores'] = $scores = $student->semesterScores->groupBy('semester');
        $semesters=StudentSemesterScore::where('student_id',$student->id)
        ->groupBy('semester')
        ->select('semester')
        ->distinct()
        ->get();
        
        foreach($semesters as $semester)
        {
            $currentSemester=$semester->semester;

            $successYear=StudentResult::where('student_id',$student->id)
            ->where('semester',$semester->semester)
            ->where('isPassed',1)
            ->first();

            if($successYear)
            {
                $educationalYearPerSemesters[$currentSemester]['education_year']=$successYear->education_year;
            }
            else
            {
                $educationalYearPerSemesters[$currentSemester]['education_year']=null;
            }
            
            $scores =StudentSemesterScore::with('subject')
            ->where('student_id',$student->id)
            ->where('semester',$semester->semester)
            ->get();
            $totalCredits=0;
            $totalScores=0;
            $averageScores=0;
            $result='';
            $subjectsCount=$scores->count();
            if($maxSubjectsCount<$subjectsCount)
            {
                $studentData['maxSubjectsCount'] = $maxSubjectsCount = $subjectsCount;
            }
           
            foreach( $scores as  $score)
            {
                $studentScores[$currentSemester][$score->subject_id]['subject']=$score->subject->title;
                $studentScores[$currentSemester][$score->subject_id]['credits']=$score->subject->credits;
                $studentScores[$currentSemester][$score->subject_id]['education_year']=$score->education_year;
                $studentScores[$currentSemester][$score->subject_id]['chance1']=$score->chance_one;
                $studentScores[$currentSemester][$score->subject_id]['chance2']=$score->chance_two;
                $studentScores[$currentSemester][$score->subject_id]['chance3']=$score->chance_three;
                $studentScores[$currentSemester][$score->subject_id]['chance4']=$score->chance_four;

                $studentScores[$currentSemester][$score->subject_id]['success_score']=$score->success_score;
                $studentScores[$currentSemester][$score->subject_id]['success_chance']=$score->success_chance;
                $studentScores[$currentSemester][$score->subject_id]['score_with_multiply']=$score->subject->credits * $score->success_score;
                $studentScores[$currentSemester][$score->subject_id]['category']=getGrade($score->success_score);
                $totalCredits+=$score->subject->credits;
                $totalCreditsAllSemesters+=$score->subject->credits;
                $totalScores+=$studentScores[$currentSemester][$score->subject_id]['score_with_multiply'];
                $totalScoresAllSemesters+=$studentScores[$currentSemester][$score->subject_id]['score_with_multiply'];
            }
            if($totalCredits > 0 )
            {
                $averageScores=round($totalScores/$totalCredits,2);
            }
            else{
                $averageScores=0;
            }
            
            $studentResult[$currentSemester]['totalCredits']=$totalCredits;
            $studentResult[$currentSemester]['totalScores']=$totalScores;
            $studentResult[$currentSemester]['averageScores']=$averageScores;
            if($averageScores < $this->MIN_SCORE_FOR_PASSED_SEMESTER)
            {
                $result= __('general.success_with_warning_semester');
            }
            else
            {
                $result= __('general.success_semester');
            }
            $studentResult[$currentSemester]['result']=$result;
        }
        if($totalCreditsAllSemesters > 0 )
        {
            $averageScoresAllSemesters=round($totalScoresAllSemesters/$totalCreditsAllSemesters,2);
        }
        else{
            $averageScoresAllSemesters=0;
        }

        $studentData['educationalYearPerSemesters'] = $educationalYearPerSemesters;
        $studentData['studentScores'] = $studentScores;
        $studentData['studentResult'] = $studentResult;
        $studentData['scores'] = $scores;
        $studentData['averageScoresAllSemesters'] = $averageScoresAllSemesters;
        $studentData['totalScoresAllSemesters'] = $totalScoresAllSemesters;
        $studentData['totalCreditsAllSemesters'] = $totalCreditsAllSemesters;

        return $studentData;

    }

}