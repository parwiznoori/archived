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
use App\Models\StudentResult;


class SemesterBasedResultsService
{
    public function MIN_SCORE_FOR_PASSED_EXAM()
    {
        $systemVariable=SystemVariable::where('name','MIN_SCORE_FOR_PASSED_EXAM')->first();
        $MIN_SCORE_FOR_PASSED_EXAM=$systemVariable->user_value;
        return $MIN_SCORE_FOR_PASSED_EXAM;
    }

    public function MIN_SCORE_FOR_PASSED_SEMESTER()
    {
        $systemVariable=SystemVariable::where('name','MIN_SCORE_FOR_PASSED_SEMESTER')->first();
        $MIN_SCORE_FOR_PASSED_SEMESTER=$systemVariable->user_value;
        return $MIN_SCORE_FOR_PASSED_SEMESTER;
    }

    public function find_group(Request $request)
    {
        $group= Group::find($request->groups);
        return $group;
    }

    public function find_students_in_group(Request $request,$group)
    {
        $students = $group ? $group->students : null;
        return $students;
    }

    public function find_students_in_group_with_at_least_enrollment_in_one_course(Request $request,$group,$coursesIdArray)
    {
        $students_list = \DB::table('course_student')
        ->whereIn('course_id',$coursesIdArray)
        ->select('student_id')
        ->distinct()
        ->pluck('student_id')
        ->all();
        $students=Student::whereIn('id',$students_list)
        ->orderBy('kankor_year','desc') 
        ->orderBy('name')
        ->orderBy('last_name')
        ->get();
        return $students;
    }

    public function course_specification($semester,$group_id)
    {
        $courseSpecification=Course::join('course_group', 'course_id', '=', 'courses.id')
        ->join('groups', 'group_id', '=', 'groups.id')
        ->where('courses.semester',$semester)
        ->where('groups.id',$group_id)
        ->select('courses.*')
        ->first();

        return $courseSpecification;
    }

    public function course_subjects($semester,$group_id)
    {
        $courseSubjects = Course::with('subject')
        ->join('course_group', 'course_id', '=', 'courses.id')
        ->join('groups', 'group_id', '=', 'groups.id')
        ->where('courses.semester',$semester)
        ->where('groups.id',$group_id)
        ->select('courses.*')
        ->get();

        return $courseSubjects;
    }

    public function get_students_id_array($students)
    {
        $j=0;
        $studentsIdArray=array();
        foreach($students as $student)
        {
            $studentsIdArray[$j]=$student->id;
            $j++;
        }
        return $studentsIdArray;
    }

    public function get_courses_id_array($courseSubjects)
    {
        $j=0;
        $coursesIdArray=array();
        foreach($courseSubjects as $course)
        {
            $coursesIdArray[$j++]=$course->id;

        }
        return $coursesIdArray;
    }

    public function check_student_has_transfer($student_id)
    {
        $transfer=Transfer::where('student_id',$student_id)->first();
        return $transfer;  
    }

    public function check_student_has_leave($student_id)
    {
        $leave=Leave::where('student_id',$student_id)->first();
        return $leave;  
    }

    public function check_student_has_dropout($student_id)
    {
        $dropout=Dropout::where('student_id',$student_id)->first();
        return $dropout;  
    }

    public function get_students_array($students)
    {
        $j=0;
        $studentsArray=array();
        foreach($students as $student)
        {
            $studentsArray['id'][$j]=$student->id;
            $studentsArray['form_no'][$j]=$student->form_no;
            $studentsArray['name'][$j]=$student->name;
            $studentsArray['last_name'][$j]=$student->last_name;
            $studentsArray['father_name'][$j]=$student->father_name;
            $studentsArray['grandfather_name'][$j]=$student->grandfather_name;
            $studentsArray['kankor_year'][$j]=$student->kankor_year;
            $studentsArray['flag_consecutive_fail'][$j]=$student->flag_consecutive_fail;
            $j++;
        }
        return $studentsArray;

    }
    public function get_course_array($courseSubjects)
    {
        $courseArray= array();
        $i=0;
        foreach($courseSubjects as $course)
        {
            $courseArray['course_id'][$i]=$course->id;
            $courseArray['semester'][$i]=$course->semester;
            $courseArray['education_year'][$i]=$course->year;
            $courseArray['subject_id'][$i]=$course->subject_id;
            $courseArray['subject_name'][$i]=$course->subject->title;
            $courseArray['credit'][$i]=$course->subject->credits;
            $courseArray['department_id'][$i]=$course->department_id;
            $i++;
        }
        return $courseArray;
    }

    //return all courses were final approved
    public function check_all_courses_were_final_approved($courseSubjects)
    {
        $courseArray= array();
        $i=0;
        foreach($courseSubjects as $course)
        {
            if($course->course_status_id < 1 || is_null($course->course_status_id) ) // means course was not final approved
            {
                $courseArray['course_id'][$i]=$course->id;
                $courseArray['semester'][$i]=$course->semester;
                $courseArray['education_year'][$i]=$course->year;
                $courseArray['subject_id'][$i]=$course->subject_id;
                $courseArray['subject_name'][$i]=$course->subject->title;
                $courseArray['credit'][$i]=$course->subject->credits;
                $courseArray['semester'][$i]=$course->semester;
                $courseArray['department_id'][$i]=$course->department_id;
                $courseArray['code'][$i]=$course->code;
                $i++;

            }
            
        }
        return $courseArray;
    }

    //return all courses were have scores
    public function check_all_courses_were_have_scores($courseSubjects)
    {
        $courseArray= array();
        $i=0;
        foreach($courseSubjects as $course)
        {
            $score=Score::where('course_id',$course->id)->first();
            if(!$score) // means course has not scored
            {
                $courseArray['course_id'][$i]=$course->id;
                $courseArray['semester'][$i]=$course->semester;
                $courseArray['education_year'][$i]=$course->year;
                $courseArray['subject_id'][$i]=$course->subject_id;
                $courseArray['subject_name'][$i]=$course->subject->title;
                $courseArray['credit'][$i]=$course->subject->credits;
                $courseArray['department_id'][$i]=$course->department_id;
                $courseArray['code'][$i]=$course->code;
                $i++;
            }
            
        }
        return $courseArray;
    }

    public function which_chance_student_passed_course($total=null,$chance_two=null,$chance_three=null,$chance_four=null,$MIN_SCORE_FOR_PASSED_EXAM=55)
    {
        $data['success_score']=null;
        $data['success_chance']=null;

        if(isset($total) &&  $total >= $MIN_SCORE_FOR_PASSED_EXAM)
        {
            $data['success_score']=$total;
            $data['success_chance']=1;
            return $data;
        }
    
        if(isset($chance_two) &&  $chance_two >= $MIN_SCORE_FOR_PASSED_EXAM)
        {
            $data['success_score']=$chance_two;
            $data['success_chance']=2;
            return $data;
        }
        if(isset($chance_three) &&  $chance_three >= $MIN_SCORE_FOR_PASSED_EXAM)
        {
            $data['success_score']=$chance_three;
            $data['success_chance']=3;
            return $data;
        }
        if(isset($chance_four) &&  $chance_four >= $MIN_SCORE_FOR_PASSED_EXAM)
        {
            $data['success_score']=$chance_four;
            $data['success_chance']=4;
            return $data;
        }
    
        return $data;

    }

    /*
    return all scores of all student in one semster
    */
    public function get_students_results($students,$courseArray,$studentsScoresArray,$MIN_SCORE_FOR_PASSED_EXAM,$MIN_SCORE_FOR_PASSED_SEMESTER)
    {
        $j=0;
        $i=0;
        $studentsResults=array();
        $studentGradeArray=array();
        $result_array=array();
        $studentsData=array();
        $numberOfFailedCredits=0;
        $numberOfPassedCredits=0;
        $deprivedStudentNumbers=0;
        $absentStudentNumbers=0;
        $passedStudentNumbers=0;
        $failedStudentNumbers=0;
        $failedAverageStudentNumbers=0;
        foreach($students as $student)
        {
            $student_id=$student->id;

            $studentData=$this->get_student_results_in_semester($student,$courseArray,$studentsScoresArray,$MIN_SCORE_FOR_PASSED_EXAM,$MIN_SCORE_FOR_PASSED_SEMESTER);

            $scoresArray[$student_id]=$studentData['studentScoresArray'];
            $studentsResults[$student_id]=$studentData['studentResults'];
            
            if($studentsResults[$student->id]['numberOfDeprivedInExams'] > 0)
            {
                $deprivedStudentNumbers += $studentsResults[$student->id]['numberOfDeprivedInExams'];
            }

            if($studentsResults[$student->id]['numberOfAbsentsInExams'] > 0)
            {
                $absentStudentNumbers += $studentsResults[$student->id]['numberOfAbsentsInExams'];
            }

            if($studentsResults[$student->id]['averageScore'] >= $MIN_SCORE_FOR_PASSED_SEMESTER and $studentsResults[$student->id]['numberOfFailedCredits']==0)
            {
                $passedStudentNumbers++;
            }
            
            if(
                ($studentsResults[$student->id]['averageScore'] > 0 ) and
                ($studentsResults[$student->id]['averageScore'] < $MIN_SCORE_FOR_PASSED_SEMESTER) && ($studentsResults[$student->id]['numberOfFailedCredits'] ==0) 
            )
            {
                $failedAverageStudentNumbers++;
            }
            if($studentsResults[$student->id]['numberOfFailedCredits'] > 0  and $studentsResults[$student->id]['averageScore'] > 0)
            {
                $failedStudentNumbers++;
            }
        }
        $studentsData['studentsResults']=$studentsResults;
        $studentsData['scoresArray']=$scoresArray;
        $studentsData['failedStudentNumbers']=$failedStudentNumbers;
        $studentsData['failedAverageStudentNumbers']=$failedAverageStudentNumbers;
        $studentsData['passedStudentNumbers']=$passedStudentNumbers;
        $studentsData['deprivedStudentNumbers']=$deprivedStudentNumbers;
        $studentsData['absentStudentNumbers']=$absentStudentNumbers;

        return  $studentsData;
    }

    public function get_student_results_in_semester($student,$courseArray,$studentsScoresArray,$MIN_SCORE_FOR_PASSED_EXAM,$MIN_SCORE_FOR_PASSED_SEMESTER)
    {
        $studentData=array();
        $totalScore=0;
        $averageScore=0;
        $result=0;
        $totalCredit=0;
        $grade=0;
        $numberOfAbsentsInExams=0;
        $numberOfDeprivedInExams=0;
        $numberOfFailedCredits=0;
        $numberOfPassedCredits=0;
        $flagConsecutiveFail=$student->flag_consecutive_fail;
        $numberOfFailedSubjetsByChance1=0;
        $numberOfFailedSubjetsByChance2=0;
        $numberOfFailedSubjetsByChance3=0;
        $numberOfFailedSubjetsByChance4=0;
        $finalSubjectIn4Chances=0;
        $final_score=0;
        $totalPresents=0;
        $totalAbsents=0;
        $student_id=$student->id;
        $studentScoresArray=array();
        $studentSemesterScores=array();
        $index=0;
            
        for($i=0;$i<count($courseArray['course_id']);$i++)
        {
            $course_id=$courseArray['course_id'][$i];
            $subject_id=$courseArray['subject_id'][$i];
            $subject_credit= $courseArray['credit'][$i];
            $department_id=$courseArray['department_id'][$i];
            $semester=$courseArray['semester'][$i];
            $education_year=$courseArray['education_year'][$i];

            /*
            fetch all scores of student with courses that was enrollmented
            */
            $studentScore=$studentsScoresArray->where('student_id', $student_id)
            ->where('course_id',$course_id)->first();

            if($studentScore) // check student has score in this cousre or not
            {
                $finalSubjectIn4Chances=0;

                $final_score=(isset($studentScore->final) ? $studentScore->final : null); //final exam score
                $present=(isset($studentScore->present) ? $studentScore->present : 0); //present
                $totalPresents+=$present;
                $absent=(isset($studentScore->absent) ? $studentScore->absent : 0); //absent
                $totalAbsents+=$absent;
                $absent_exam=(isset($studentScore->absent_exam) ? $studentScore->absent_exam : null); //absent_exam
                $excuse_exam=(isset($studentScore->excuse_exam) ? $studentScore->excuse_exam : null); //excuse_exam
                $deprived=(isset($studentScore->deprived) ? $studentScore->deprived : null); //deprived
                    
                $studentScoresArray[$course_id][1]=
                (isset($studentScore->total) ? $studentScore->total : null); //chance 1
                $studentScoresArray[$course_id][2]=
                (isset($studentScore->chance_two) ? $studentScore->chance_two : null);//chance 2
                $studentScoresArray[$course_id][3]=
                (isset($studentScore->chance_three) ? $studentScore->chance_three : null);//chance 3
                $studentScoresArray[$course_id][4]=
                (isset($studentScore->chance_four) ? $studentScore->chance_four : null);//chance 4

                $student_deprived=is_this_student_deprived_from_exam($present,$absent,$deprived);
                $studentScoresArray[$course_id]['deprived']=$student_deprived;
                if( $student_deprived > 0)
                {
                    $numberOfDeprivedInExams++;
                }
                $student_absent=is_this_student_absent_in_final_exam($final_score,$present,$absent,$absent_exam,$excuse_exam,$deprived);
                $studentScoresArray[$course_id]['absent_exam']=$student_absent;
                if( $student_absent > 0)
                {
                    $numberOfAbsentsInExams++;
                }
    
                $passed_course=is_this_student_passed_this_course($studentScoresArray[$course_id][1],$studentScoresArray[$course_id][2],$studentScoresArray[$course_id][3],$studentScoresArray[$course_id][4],$MIN_SCORE_FOR_PASSED_EXAM);
              
                $studentScoresArray[$course_id]['this_semester']=1;
                if($passed_course)
                {
                    $studentScoresArray[$course_id]['passed']=1;
                    $numberOfPassedCredits+=$subject_credit;
                }
                else
                {
                    $studentScoresArray[$course_id]['passed']=0;
                }   
    
                if(isset($studentScore->total))
                {
                    if($studentScore->total < $MIN_SCORE_FOR_PASSED_EXAM)
                    {
                        $numberOfFailedSubjetsByChance1++;
                    }
                    $studentScoresArray[$course_id]['withCredit']= $studentScore->total * $subject_credit; 
                }
                if(isset($studentScore->chance_two))
                {
                    if($studentScore->chance_two < $MIN_SCORE_FOR_PASSED_EXAM)
                    {
                        $numberOfFailedSubjetsByChance2++;
                    }
                    $studentScoresArray[$course_id]['withCredit']= $studentScore->chance_two * $subject_credit;
                }
                if(isset($studentScore->chance_three))
                {
                    if($studentScore->chance_three < $MIN_SCORE_FOR_PASSED_EXAM)
                    {
                        $numberOfFailedSubjetsByChance3++;
                    }
                    
                    $studentScoresArray[$course_id]['withCredit']= $studentScore->chance_three * $subject_credit;
                }
                if(isset($studentScore->chance_four))
                {
                    if($studentScore->chance_four < $MIN_SCORE_FOR_PASSED_EXAM)
                    {
                        $finalSubjectIn4Chances=1;
                        $numberOfFailedSubjetsByChance4++;
                    }
                    $studentScoresArray[$course_id]['withCredit']= $studentScore->chance_four * $subject_credit;
                }
    
                if(!isset($studentScore->chance_four) && !isset($studentScore->chance_three) && !isset($studentScore->chance_two) && !isset($studentScore->total) && !isset($studentScore->final))
                {
                    $studentScoresArray[$course_id]['withCredit']=0;
                    $studentScoresArray[$course_id]['passed']=0;
                }
    
                $totalCredit += $subject_credit;
                $totalScore += $studentScoresArray[$course_id]['withCredit'];

            }
            else{

                $studentScore=Score::where('student_id',$student_id)
                ->where('subject_id',$subject_id)
                ->where('course_id','!=',$course_id)->first();

                $finalSubjectIn4Chances=0;
            
                $final_score=(isset($studentScore->final) ? $studentScore->final : null); //final exam score
                $present=(isset($studentScore->present) ? $studentScore->present : 0); //present
                $totalPresents+=$present;
                $absent=(isset($studentScore->absent) ? $studentScore->absent : 0); //absent
                $totalAbsents+=$absent;
                $absent_exam=(isset($studentScore->absent_exam) ? $studentScore->absent_exam : null); //absent_exam
                $excuse_exam=(isset($studentScore->excuse_exam) ? $studentScore->excuse_exam : null); //excuse_exam
                $deprived=(isset($studentScore->deprived) ? $studentScore->deprived : null); //deprived
                
                $studentScoresArray[$course_id][1]=
                (isset($studentScore->total) ? $studentScore->total : null); //chance 1
                $studentScoresArray[$course_id][2]=
                (isset($studentScore->chance_two) ? $studentScore->chance_two : null);//chance 2
                $studentScoresArray[$course_id][3]=
                (isset($studentScore->chance_three) ? $studentScore->chance_three : null);//chance 3
                $studentScoresArray[$course_id][4]=
                (isset($studentScore->chance_four) ? $studentScore->chance_four : null);//chance 4
                
                $student_deprived=is_this_student_deprived_from_exam($present,$absent,$deprived);
                $studentScoresArray[$course_id]['deprived']=$student_deprived;
                if( $student_deprived > 0)
                {
                    $numberOfDeprivedInExams++;
                }

                $student_absent=is_this_student_absent_in_final_exam($final_score,$present,$absent,$absent_exam,$excuse_exam,$deprived);
                $studentScoresArray[$course_id]['absent_exam']=$student_absent;
                if( $student_absent > 0)
                {
                    $numberOfAbsentsInExams++;
                }
               
    
                $passed_course=is_this_student_passed_this_course($studentScoresArray[$course_id][1],$studentScoresArray[$course_id][2],$studentScoresArray[$course_id][3],$studentScoresArray[$course_id][4],$MIN_SCORE_FOR_PASSED_EXAM);
            
                $studentScoresArray[$course_id]['this_semester']=0;
                if($passed_course)
                {
                    $studentScoresArray[$course_id]['passed']=1;
                    $numberOfPassedCredits+=$subject_credit;
                }
                else
                {
                    $studentScoresArray[$course_id]['passed']=0;
                }
    
                if(isset($studentScore->total))
                {
                    if($studentScore->total < $MIN_SCORE_FOR_PASSED_EXAM)
                    {
                        $numberOfFailedSubjetsByChance1++;
                    }
                    $studentScoresArray[$course_id]['withCredit']= $studentScore->total * $subject_credit; 
                }
                if(isset($studentScore->chance_two))
                {
                    if($studentScore->chance_two < $MIN_SCORE_FOR_PASSED_EXAM)
                    {
                        $numberOfFailedSubjetsByChance2++;
                    }
                    $studentScoresArray[$course_id]['withCredit']= $studentScore->chance_two * $subject_credit;
                }
                if(isset($studentScore->chance_three))
                {
                    if($studentScore->chance_three < $MIN_SCORE_FOR_PASSED_EXAM)
                    {
                        $numberOfFailedSubjetsByChance3++;
                    }
                    $studentScoresArray[$course_id]['withCredit']= $studentScore->chance_three * $subject_credit;
                }
                if(isset($studentScore->chance_four))
                {
                    if($studentScore->chance_four < $MIN_SCORE_FOR_PASSED_EXAM)
                    {
                        $finalSubjectIn4Chances=1;
                        $numberOfFailedSubjetsByChance4++;
                    }
                    $studentScoresArray[$course_id]['withCredit']= $studentScore->chance_four * $subject_credit;
                }
    
                if(!isset($studentScore->chance_four) && !isset($studentScore->chance_three) && !isset($studentScore->chance_two) && !isset($studentScore->total) && !isset($studentScore->final))
                {
                    $studentScoresArray[$course_id]['withCredit']=0;
                    $studentScoresArray[$course_id]['passed']=0;
                }
                $totalCredit += $subject_credit;
                $totalScore += $studentScoresArray[$course_id]['withCredit'];
            }  
        }

        $numberOfFailedCredits=$totalCredit-$numberOfPassedCredits;
        $totalCreditsDivideBy2ForBenefitOfStudent = ceil($totalCredit/2);
        // check if studentd fail in more than 50% of credits in same semster
        //student will be study these credits in next year
        //flag in tables students will changed

        /*
        check all situations that causes student get in to next year
        */
        if($numberOfFailedCredits > $totalCreditsDivideBy2ForBenefitOfStudent && $totalScore > 0 )
        {
            $studentResults['nextYear']=1;
            $studentResults['increaseSemester']=0;
        }
        else
        {
            $studentResults['nextYear']=0;
            $studentResults['increaseSemester']=1;
        }
        /*
        student that has not any score => can not increase semesetr
        */
        if($totalScore == 0)
        {
            $studentResults['increaseSemester']=0;
        }
        
        //check if student fail in 3 or more subjects by chance2  in this semester => student will be failed and next year
        if($numberOfFailedSubjetsByChance2 >= 3)
        {
            $studentResults['nextYearByFailed3OrMoreSubjetsByChance2']=1;
            $studentResults['nextYear']=1;
            $studentResults['increaseSemester']=0;
        }
        else{
            $studentResults['nextYearByFailed3OrMoreSubjetsByChance2']=0;
        }
        //check if student fail in 2 or more subjects by chance 3 in this semester => student will be failed and next year
        if($numberOfFailedSubjetsByChance3 >= 2)
        {
            $studentResults['nextYearByFailed2OrMoreSubjetsByChance3']=1;
            $studentResults['nextYear']=1;
            $studentResults['increaseSemester']=0;
        }
        else{
            $studentResults['nextYearByFailed2OrMoreSubjetsByChance3']=0;
        }
        //check if students fail in asubject by 4 chances => next year
        if($finalSubjectIn4Chances == 1)
        {
            $studentResults['finalSubjectIn4Chances']=1; //drop from university
            $studentResults['increaseSemester']=0;
        }
        else{
            $studentResults['finalSubjectIn4Chances']=0;
        }
        /* check situations that causes student drop from university */
        //check if student failed for second time consecutive => student will be fired from university
        if( $studentResults['nextYear'] == 1 && $flagConsecutiveFail >= 1 )
        {
            $studentResults['nextYear']=2;
        }
        //////////////////////////////////////////////////////////////////////////////
        /**
         * check the results of semester for student
         */
        if($totalCredit > 0 )
        {
            $averageScore= round($totalScore/$totalCredit,2);
        }
        else
        {
            $averageScore=0;
        }

        $result=0; 
        //student passed all credits in semester => increase semester
        if($numberOfFailedCredits==0 && $totalScore > 0 )
        {
            $result=1;
        }
        //student passed atleast half of credits in semester => increase semester
        if($numberOfFailedCredits <= $totalCreditsDivideBy2ForBenefitOfStudent && $totalScore > 0 )
        {
            $result=1;
        }

        /**
         * check all situations that causes students has not any score => may be leave,dropout,transfer
         */
        if($totalScore==0)
        {
            $transfer=$this->check_student_has_transfer($student_id);
            if($transfer)
            {
                $transfer_details=$transfer->fromDepartment->university->name.' '.$transfer->fromDepartment->name.' سمستر ' .$transfer->semester;
                $studentResults['has_transfer']=1;
                $studentResults['transfer_detail']=$transfer_details;
            }
            else{
                $studentResults['has_transfer']=0;
                $studentResults['transfer_detail']='';
            }  

            $leave=$this->check_student_has_leave($student_id);
            if($leave)
            {
                $leave_details=$leave->leave_year.' سمستر '.$leave->semester;
                $studentResults['has_leave']=1;
                $studentResults['leave_detail']=$leave_details;
            }
            else{
                $studentResults['has_leave']=0;
                $studentResults['leave_detail']='';
            }  

            $dropout=$this->check_student_has_dropout($student_id);
            if($dropout)
            {
                $dropout_details=$dropout->year.' سمستر '.$dropout->semester;
                $studentResults['has_dropout']=1;
                $studentResults['dropout_detail']=$dropout_details;
            }
            else{
                $studentResults['has_dropout']=0;
                $studentResults['dropout_detail']='';
            }
        }
        else
        {
            $studentResults['has_transfer']=0;
            $studentResults['transfer_detail']='';
            $studentResults['has_leave']=0;
            $studentResults['leave_detail']='';
            $studentResults['has_dropout']=0;
            $studentResults['dropout_detail']='';

        }

        $studentResults['numberOfAbsentsInExams']=$numberOfAbsentsInExams;
        $studentResults['numberOfDeprivedInExams']=$numberOfDeprivedInExams;
        
        $studentResults['totalScore']=$totalScore;
        $studentResults['numberOfFailedCredits']=$numberOfFailedCredits;
        $studentResults['totalCredit']=$totalCredit;
        $studentResults['averageScore']=$averageScore;
        $studentResults['result']=$result;//student can go to next semester
        $grade=$studentResults['grade']=getGrade($averageScore);

        $studentResults['totalPresents']=$totalPresents;
        $studentResults['totalAbsents']=$totalAbsents;
        $increaseSemester=$studentResults['increaseSemester'];
        $isPassed=0;
        $kankor_year=$student->kankor_year;
        
        if($numberOfFailedCredits==0 && $totalScore > 0 )
        {
            $isPassed=1;
        }else{
            $isPassed=0;
        }

        \DB::transaction(function () use ($student_id, $kankor_year, $semester, $department_id, $education_year,$isPassed, $grade, $averageScore,$increaseSemester,$totalCredit,$numberOfPassedCredits){
            $result_semester=StudentResult::addNewStatusResult($student_id, $kankor_year, $semester, $department_id, $education_year,$isPassed, $grade, $averageScore,$increaseSemester,$totalCredit,$numberOfPassedCredits);
        });
        
       
        ///////////////////////////////////////////////////

        $studentData['totalCredit']=$totalCredit;
        $studentData['totalScore']=$totalScore;
        $studentData['studentScoresArray']=$studentScoresArray;
        $studentData['totalPresents']=$totalPresents;
        $studentData['totalAbsents']=$totalAbsents;
        $studentData['numberOfPassedCredits']=$numberOfPassedCredits;
        $studentData['studentResults']=$studentResults;

        return $studentData;
        
    }
}