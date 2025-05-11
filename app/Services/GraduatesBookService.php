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
use App\Models\Student;
use App\Models\University;
use App\Models\Subject;
use App\Models\Transfer;
use App\Models\StudentSemesterScore;
use App\Models\GraduatedStudent;


class GraduatesBookService
{
    /*
    return all scores of all student in one semster
    */
    public function get_registration_of_graduate_documents($university_id,$department_id,$graduated_year,$grade_id)
    {
        $graduatesStudents=array();
        
        $graduatedStudents = GraduatedStudent::select('*')
        ->with('university')
        ->with('department')
        ->with('student')
        ->with('grade')
        ->where('university_id',$university_id)
        ->where('department_id',$department_id)
        ->where('grade_id',$grade_id)
        ->where('graduated_year',$graduated_year)->get();


        $j=0;
        $i=0;
       
        foreach($graduatedStudents as $graduateStudent)
        {
            $student_id=$graduateStudent->student->id;
            $graduatesStudents[$i]['student_id'] = $graduateStudent->student->id;
            $graduatesStudents[$i]['form_no'] = $graduateStudent->student->form_no;
            $graduatesStudents[$i]['name'] = $graduateStudent->student->name;
            $graduatesStudents[$i]['last_name'] = $graduateStudent->student->last_name;
            $graduatesStudents[$i]['full_name'] = $graduateStudent->student->full_name;
            $graduatesStudents[$i]['father_name'] = $graduateStudent->student->father_name;
            $graduatesStudents[$i]['grandfather_name'] = $graduateStudent->student->grandfather_name;
           
            $tazkira = explode('!@#', $graduateStudent->student->tazkira);
           
            $graduatesStudents[$i]['tazkira'] = $tazkira[3] ?? null;
            $graduatesStudents[$i]['birthdate'] = $graduateStudent->student->birthdate;
            $graduatesStudents[$i]['phone'] = $graduateStudent->student->phone;
            $graduatesStudents[$i]['kankor_year'] = $graduateStudent->student->kankor_year;
            $graduatesStudents[$i]['graduated_year'] = $graduateStudent->graduated_year;
            $graduatesStudents[$i]['photo_url'] = $graduateStudent->student->photo();
            $graduatesStudents[$i]['diploma_photo'] = $graduateStudent->student->diploma_photo;
            $graduatesStudents[$i]['monograph_title'] = $graduateStudent->student->monograph ?$graduateStudent->student->monograph->title : '' ;
            $graduatesStudents[$i]['mongraph_defense_date'] = $graduateStudent->student->monograph ? $graduateStudent->student->monograph->defense_date : '';
            $graduatesStudents[$i]['mongraph_teacher'] = $graduateStudent->student->monograph ? $graduateStudent->student->monograph->teacher->teacherAcademic->title ." ".$graduateStudent->student->monograph->teacher->full_name : '';
            $graduatesStudents[$i]['manual_graduated'] = $graduateStudent->manual_graduated;
            $graduatesStudents[$i]['description'] = $graduateStudent->description;
            $graduatesStudents[$i]['registeration_date'] = $graduateStudent->registeration_date;
            if($graduateStudent->registeration_date)
            {
                $registeration_date=explode('/',$graduateStudent->registeration_date);
                $date1=\Morilog\Jalali\CalendarUtils::toGregorian($registeration_date[0],$registeration_date[1], $registeration_date[2]);
                $registeration_date_gregorian=implode('-',$date1);
            }
            else
            {
                $registeration_date_gregorian = '';
            }
            $graduatesStudents[$i]['registeration_date_gregorian'] = $registeration_date_gregorian;

            $graduatesStudents[$i]['received_diploma'] = $graduateStudent->received_diploma;
            $graduatesStudents[$i]['received_diploma_date'] = $graduateStudent->received_diploma_date;
            $graduatesStudents[$i]['diploma_letter_number'] = $graduateStudent->diploma_letter_number;
            $graduatesStudents[$i]['diploma_letter_date'] = $graduateStudent->diploma_letter_date;
            $graduatesStudents[$i]['diploma_number'] = $graduateStudent->diploma_number;
            $graduatesStudents[$i]['received_certificate'] = $graduateStudent->received_certificate;
            $graduatesStudents[$i]['received_certificate_date'] = $graduateStudent->received_certificate_date;
            $graduatesStudents[$i]['certificate_letter_number'] = $graduateStudent->certificate_letter_number;
            $graduatesStudents[$i]['certificate_letter_date'] = $graduateStudent->certificate_letter_date;
            $graduatesStudents[$i]['received_transcript_en'] = $graduateStudent->received_transcript_en;
            $graduatesStudents[$i]['received_transcript_en_date'] = $graduateStudent->received_transcript_en_date;
            $graduatesStudents[$i]['transcript_en_letter_number'] = $graduateStudent->transcript_en_letter_number;
            $graduatesStudents[$i]['transcript_en_letter_date'] = $graduateStudent->transcript_en_letter_date;
            $graduatesStudents[$i]['received_transcript_da'] = $graduateStudent->received_transcript_da;
            $graduatesStudents[$i]['received_transcript_da_date'] = $graduateStudent->received_transcript_da_date;
            $graduatesStudents[$i]['transcript_da_letter_number'] = $graduateStudent->transcript_da_letter_number;
            $graduatesStudents[$i]['transcript_da_letter_date'] = $graduateStudent->transcript_da_letter_date;
            $graduatesStudents[$i]['received_transcript_pa'] = $graduateStudent->received_transcript_pa;
            $graduatesStudents[$i]['received_transcript_pa_date'] = $graduateStudent->received_transcript_pa_date;
            $graduatesStudents[$i]['transcript_pa_letter_number'] = $graduateStudent->transcript_pa_letter_number;
            $graduatesStudents[$i]['transcript_pa_letter_date'] = $graduateStudent->transcript_pa_letter_date;
            $graduatesStudents[$i]['hand_over_identity_card'] = $graduateStudent->hand_over_identity_card;
            $graduatesStudents[$i]['hand_over_non_responsibility_form'] = $graduateStudent->hand_over_non_responsibility_form;

            $i++;
        }
        
        return  $graduatesStudents;
    }

    public function getGraduateStudentsData($university_id,$department_id,$graduated_year,$grade_id)
    {
        $graduatesStudents = array();
        $graduatesStusentsArray = array();
        
        $graduatedStudents = GraduatedStudent::select('*')
        ->where('university_id',$university_id)
        ->where('department_id',$department_id)
        ->where('grade_id',$grade_id)
        ->where('graduated_year',$graduated_year)->get();
        $i=0;
        foreach($graduatedStudents as $graduateStudent)
        {
            $graduatesStusentsArray[$i]['graduateStudent'] = $graduateStudent;
            $graduatesStusentsArray[$i]['student_id'] = $student_id = $graduateStudent->student_id;
            $studentInstance = new StudentService($student_id);
            $graduatesStusentsArray[$i]['student'] = $student = $studentInstance->studentInformation();
            $graduatesStusentsArray[$i]['monograph'] = $monograph = $studentInstance->monographWithTeacher();
            $graduatesStusentsArray[$i]['yearOfLeave'] = $yearOfLeave = $studentInstance->getLeaveYear()->leave_year ?? 0 ;
            $std_tazkira = explode('!@#', $student->tazkira);
            $graduatesStusentsArray[$i]['tazkira'] = $tazkira = $std_tazkira[3];
            $studentData = $studentInstance->graduateStudentResults();
            $graduatesStusentsArray[$i]['transferDescribtion'] = $transferDescribtion = $studentInstance->transferDescribtion();
            $graduatesStusentsArray[$i]['studentScores'] = $studentScores = $studentData['studentScores'];
            $graduatesStusentsArray[$i]['studentResult'] = $studentResult = $studentData['studentResult'];
            $graduatesStusentsArray[$i]['educationalYearPerSemesters'] = $educationalYearPerSemesters = $studentData['educationalYearPerSemesters'];
            $graduatesStusentsArray[$i]['semestersCount'] = $semestersCount = $studentData['semestersCount'];
            $graduatesStusentsArray[$i]['scores'] = $scores = $studentData['scores'];
            $graduatesStusentsArray[$i]['totalCreditsAllSemesters'] = $totalCreditsAllSemesters = $studentData['totalCreditsAllSemesters'];
            $graduatesStusentsArray[$i]['totalScoresAllSemesters'] = $totalScoresAllSemesters = $studentData['totalScoresAllSemesters'];
            $graduatesStusentsArray[$i]['averageScoresAllSemesters'] = $averageScoresAllSemesters = $studentData['averageScoresAllSemesters'];
            $graduatesStusentsArray[$i]['maxSubjectsCount'] = $maxSubjectsCount = $studentData['maxSubjectsCount'];
           
            $i++;
        }
        
        return  $graduatesStusentsArray;
    }
}