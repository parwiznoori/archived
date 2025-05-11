<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\GraduatedStudent;
use App\Models\Monograph;
use App\Models\Scores;
use App\Models\Student;
use App\Models\StudentResult;
use App\Models\StudentSemesterScore;
use App\Models\SystemVariable;
use PDF;

class TranscriptController extends Controller
{
    public function create($id)
    {
        $graduatedStudents = GraduatedStudent::findOrFail($id);
        $student_id= $graduatedStudents->student_id;
        $student= Student::findOrFail($student_id);
        $number_of_semesters=$student->department->number_of_semesters;
        $graduatedStudents=$student->graduatedStudents;
        $email=$student->university->email;
        $MIN_SCORE_FOR_PASSED_SEMESTER=SystemVariable::where('name','MIN_SCORE_FOR_PASSED_SEMESTER')->first()->user_value;
        $studentScores=array();
        $studentResult=array();
        $semestersCount=8;
        $totalCredits=0;
        $totalScores=0;
        $averageScores=0;
        $result='';
        $maxSubjectsCount=0;
        $totalCreditsAllSemesters=0;
        $totalScoresAllSemesters=0;
        $averageScoresAllSemesters=0;
        $cgpa=0;

        $scores = $student->semesterScores->groupBy('semester');
        $semesters=StudentSemesterScore::where('student_id',$student->id)
        ->groupBy('semester')
        ->select('semester')
        ->selectRaw('max(education_year) AS education_year')
        ->orderBy('education_year')
        ->distinct()
        ->get();

        foreach($semesters as $semester)
        {
            $scores =StudentSemesterScore::leftJoin('subjects', 'students_semester_scores.subject_id', '=', 'subjects.id')
            ->where('student_id',$student->id)
            ->where('students_semester_scores.semester',$semester->semester)
            ->orderBy('title_eng')
            ->orderBy('education_year')
            ->get();
            $totalCredits=0;
            $totalScores=0;
            $averageScores=0;
            $result='';
            $subjectsCount=$scores->count();
            if($maxSubjectsCount<$subjectsCount)
            {
                $maxSubjectsCount=$subjectsCount;
            }
            $currentSemester=$semester->semester;
           
            foreach( $scores as  $score)
            {
                $studentScores[$currentSemester][$score->subject_id]['code']=$score->code;
                $studentScores[$currentSemester][$score->subject_id]['subject']=$score->title_eng;
                $studentScores[$currentSemester][$score->subject_id]['credits']=intval($score->credits);
                $studentScores[$currentSemester][$score->subject_id]['education_year']=$score->education_year;
                $studentScores[$currentSemester][$score->subject_id]['success_score']=$score->success_score;
                $studentScores[$currentSemester][$score->subject_id]['success_chance']=$score->success_chance;
                $studentScores[$currentSemester][$score->subject_id]['score_with_multiply']=$score->subject->credits * $score->success_score;
                $studentScores[$currentSemester][$score->subject_id]['category']=getGrade($score->success_score);
                $totalCredits+=$score->credits;
                $totalCreditsAllSemesters+=$score->credits;
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
            $studentResult[$currentSemester]['cgpa']=($averageScores*4)/100;
            if($averageScores<$MIN_SCORE_FOR_PASSED_SEMESTER)
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
            $cgpa= ($averageScoresAllSemesters*4)/100;
        }
        else{
            $averageScoresAllSemesters=0;
            $cgpa=0;
        }
       
        $std_tazkira = explode('!@#', $student->tazkira);
        $tazkira = $std_tazkira[3];

        // return view('students.transcript-en', [
        //       'title' => trans('general.students'),
        //     'description' => trans('general.students_list'),
        //     'student' => $student,
        //     'cgpa' => $cgpa,
        //     'studentScores' => $studentScores,
        //     'semestersCount' => $semestersCount,
        //     'studentResult' => $studentResult,
        //     'tazkira' => $tazkira,
        //     'maxSubjectsCount' => $maxSubjectsCount,
        //     'averageScoresAllSemesters' => $averageScoresAllSemesters,
        //     'totalScoresAllSemesters' => $totalScoresAllSemesters,
        //     'totalCreditsAllSemesters' => $totalCreditsAllSemesters,
        //     'graduatedStudent' => $graduatedStudents,
        // ]);
        // dd( $studentScores);

        //hfdjfhdjfhds
      
        $pdf = PDF::loadView('students.transcript-en', [
            'title' => trans('general.students'),
            'description' => trans('general.students_list'),
            'student' => $student,
            'cgpa' => $cgpa,
            'studentScores' => $studentScores,
            'semestersCount' => $semestersCount,
            'studentResult' => $studentResult,
            'tazkira' => $tazkira,
            'maxSubjectsCount' => $maxSubjectsCount,
            'averageScoresAllSemesters' => $averageScoresAllSemesters,
            'totalScoresAllSemesters' => $totalScoresAllSemesters,
            'totalCreditsAllSemesters' => $totalCreditsAllSemesters,
            'graduatedStudent' => $graduatedStudents,
            'email' => $email,
        ], [], [
            'format' => 'A4-P'
          ]);

          return $pdf->stream('transcript-english.pdf');

       

    }

    public function transcriptDari($id)
    {
        
        $graduatedStudent = GraduatedStudent::findOrFail($id);
        $student_id= $graduatedStudent->student_id;
        $student= Student::findOrFail($student_id);

        $MIN_SCORE_FOR_PASSED_SEMESTER=SystemVariable::where('name','MIN_SCORE_FOR_PASSED_SEMESTER')->first()->user_value;
        $studentScores=array();
        $studentResult=array();
        $educationalYearPerSemesters=array();
        $semestersCount=8;
        $totalCredits=0;
        $totalScores=0;
        $averageScores=0;
        $result='';
        $maxSubjectsCount=0;
        $totalCreditsAllSemesters=0;
        $totalScoresAllSemesters=0;
        $averageScoresAllSemesters=0;

        $scores = $student->semesterScores->groupBy('semester');
        $semesters=StudentSemesterScore::where('student_id',$student->id)
        ->groupBy('semester')
        ->select('semester')
        ->distinct()
        ->get();
        
        

        $monograph=Monograph::with('teacher')
        ->where('student_id',$student->id)
        ->first();

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
                $maxSubjectsCount=$subjectsCount;
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
            if($averageScores<$MIN_SCORE_FOR_PASSED_SEMESTER)
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
       

        $std_tazkira = explode('!@#', $student->tazkira);
        $tazkira = $std_tazkira[3];
      // dd($studentScores);

        // return view('students.transcript-dr', [
        //     'title' => trans('general.students'),
        //     'description' => trans('general.students_list'),
        //     'student' => $student,
        //     'educationalYearPerSemesters' => $educationalYearPerSemesters,
        //     'monograph' => $monograph,
        //     'scores' => $scores,
        //     'studentScores' => $studentScores,
        //     'semestersCount' => $semestersCount,
        //     'studentResult' => $studentResult,
        //     'tazkira' => $tazkira,
        //     'maxSubjectsCount' => $maxSubjectsCount,
        //     'averageScoresAllSemesters' => $averageScoresAllSemesters,
        //     'totalScoresAllSemesters' => $totalScoresAllSemesters,
        //     'totalCreditsAllSemesters' => $totalCreditsAllSemesters,
        // ]);

        $pdf = PDF::loadView('students.transcript-dr', [
            'title' => trans('general.students'),
            'description' => trans('general.students_list'),
            'student' => $student,
            'educationalYearPerSemesters' => $educationalYearPerSemesters,
            'monograph' => $monograph,
            'scores' => $scores,
            'studentScores' => $studentScores,
            'semestersCount' => $semestersCount,
            'studentResult' => $studentResult,
            'tazkira' => $tazkira,
            'maxSubjectsCount' => $maxSubjectsCount,
            'averageScoresAllSemesters' => $averageScoresAllSemesters,
            'totalScoresAllSemesters' => $totalScoresAllSemesters,
            'totalCreditsAllSemesters' => $totalCreditsAllSemesters,
        ], [], [
            'format' => 'A4-L'
          ]);

        return $pdf->stream('transcript-dari.pdf');

    }

    public function transptPashto($id)
    {
        $graduatedStudent = GraduatedStudent::findOrFail($id);
        $student_id= $graduatedStudent->student_id;
        $student= Student::findOrFail($student_id);

        $MIN_SCORE_FOR_PASSED_SEMESTER=SystemVariable::where('name','MIN_SCORE_FOR_PASSED_SEMESTER')->first()->user_value;
        $studentScores=array();
        $studentResult=array();
        $educationalYearPerSemesters=array();
        $semestersCount=8;
        $totalCredits=0;
        $totalScores=0;
        $averageScores=0;
        $result='';
        $maxSubjectsCount=0;
        $totalCreditsAllSemesters=0;
        $totalScoresAllSemesters=0;
        $averageScoresAllSemesters=0;

        $scores = $student->semesterScores->groupBy('semester');
        $semesters=StudentSemesterScore::where('student_id',$student->id)
        ->groupBy('semester')
        ->select('semester')
        ->distinct()
        ->get();
        
        

        $monograph=Monograph::with('teacher')
        ->where('student_id',$student->id)
        ->first();

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
                $maxSubjectsCount=$subjectsCount;
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
            if($averageScores<$MIN_SCORE_FOR_PASSED_SEMESTER)
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
       

        $std_tazkira = explode('!@#', $student->tazkira);
        $tazkira = $std_tazkira[3];
     
        // return view('students.transcript-ps', [
        //     'title' => trans('general.students'),
        //     'description' => trans('general.students_list'),
        //     'student' => $student,
        //     'scores' => $scores,
        //     'studentScores' => $studentScores,
        //     'semestersCount' => $semestersCount,
        //     'studentResult' => $studentResult,
        //     'tazkira' => $tazkira,
        //     'maxSubjectsCount' => $maxSubjectsCount,
        //     'averageScoresAllSemesters' => $averageScoresAllSemesters,
        //     'totalScoresAllSemesters' => $totalScoresAllSemesters,
        //     'totalCreditsAllSemesters' => $totalCreditsAllSemesters,
        // ]);

        $pdf = PDF::loadView('students.transcript-ps', [
            'title' => trans('general.students'),
            'description' => trans('general.students_list'),
            'student' => $student,
            'educationalYearPerSemesters' => $educationalYearPerSemesters,
            'monograph' => $monograph,
            'scores' => $scores,
            'studentScores' => $studentScores,
            'semestersCount' => $semestersCount,
            'studentResult' => $studentResult,
            'tazkira' => $tazkira,
            'maxSubjectsCount' => $maxSubjectsCount,
            'averageScoresAllSemesters' => $averageScoresAllSemesters,
            'totalScoresAllSemesters' => $totalScoresAllSemesters,
            'totalCreditsAllSemesters' => $totalCreditsAllSemesters,
        ], [], [
            'format' => 'A4-L'
          ]);

        return $pdf->stream('transcript-pashto.pdf');

    }

    public function displayStudentScores($student)
    {
        // dd($student);
        $scores = $student->scores->groupBy('semester');
        return view('students.student-scores', [
            'title' => trans('general.students'),
            'student' => $student,
            'scores' => $scores
        ]);
    }


}
