<?php

namespace App\Http\Controllers\Course;

use App\Exports\CourseStudentsExport;
use App\Http\Controllers\Controller;
use App\Models\Score;
use App\Models\SystemVariable;
use Excel;
use Illuminate\Http\Request;

class ScoreSheetController extends Controller
{
    public function print(Request $request, $course)
    {       
        $course->loadStudentsAndScoresAndSemesterDeprived();
        // dd($course);
        // return view('course.score-sheet.print', compact('course', 'request'));
       
        // $pdf = \PDF::loadView('course.score-sheet.print', compact('course', 'request'), [], [
        //     //'format' => 'A4-L',
        //     'direction' => 'rlt'
        // ]);

        // return $pdf->stream($course->code.'.pdf');
    }  
    
    public function export_excel_chanc1(Request $request, $course)
    {   
        // dd($course);
        $course->loadStudentsAndScoresAndSemesterDeprived();
       
        return Excel::download(new CourseStudentsExport($course), $course->code.'_scores.xlsx');
           
    } 
    
    public function print_chance1(Request $request, $course)
    {   
        $course->loadStudentsAndScoresAndSemesterDeprived();
        $MIN_SCORE_FOR_PASSED_EXAM=SystemVariable::where('name','MIN_SCORE_FOR_PASSED_EXAM')->first()->user_value;

        if($request->withScores == 1)
        {
            // return view('course.score-sheet.print_chance1_with_scores', compact('course', 'request','MIN_SCORE_FOR_PASSED_EXAM'));
            $pdf = \PDF::loadView('course.score-sheet.print_chance1_with_scores', compact('course', 'request','MIN_SCORE_FOR_PASSED_EXAM'), [], [
                //'format' => 'A4-L',
                'direction' => 'rlt'
            ]);

        } 
        else{
            // return view('course.score-sheet.print_chance1', compact('course', 'request','MIN_SCORE_FOR_PASSED_EXAM'));
            $pdf = \PDF::loadView('course.score-sheet.print_chance1', compact('course', 'request','MIN_SCORE_FOR_PASSED_EXAM'), [], [
                //'format' => 'A4-L',
                'direction' => 'rlt'
            ]);
        }   
        
        return $pdf->stream($course->code.'-print_chance1'.'.pdf');
    } 
    
    public function print_chance2(Request $request, $course)
    {   
        $course_id=$course->id;
        $MIN_SCORE_FOR_PASSED_EXAM=SystemVariable::where('name','MIN_SCORE_FOR_PASSED_EXAM')->first()->user_value;
        $min_score=$MIN_SCORE_FOR_PASSED_EXAM;

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

        if($request->withScores == 1)
        {
            // return view('course.score-sheet.print_chance2_with_scores', compact('course', 'request','MIN_SCORE_FOR_PASSED_EXAM','scores'));
            $pdf = \PDF::loadView('course.score-sheet.print_chance2_with_scores', compact('course', 'request','MIN_SCORE_FOR_PASSED_EXAM','scores'), [], [
                //'format' => 'A4-L',
                'direction' => 'rlt'
            ]);

        } 
        else{
            // return view('course.score-sheet.print_chance2', compact('course', 'request','MIN_SCORE_FOR_PASSED_EXAM','scores'));
            $pdf = \PDF::loadView('course.score-sheet.print_chance2', compact('course', 'request','MIN_SCORE_FOR_PASSED_EXAM','scores'), [], [
                //'format' => 'A4-L',
                'direction' => 'rlt'
            ]);
        }   
        
        return $pdf->stream($course->code.'-print_chance2'.'.pdf');
    }  

    public function print_chance3(Request $request, $course)
    {   
        $min_score=$MIN_SCORE_FOR_PASSED_EXAM=SystemVariable::where('name','MIN_SCORE_FOR_PASSED_EXAM')->first()->user_value;
       

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

        // $scores=Score::with('course')->with('student')
        // ->where('course_id',$course->id)
        // ->where('chance_two','<',$MIN_SCORE_FOR_PASSED_EXAM)
        // ->get();     

        if($request->withScores == 1)
        {
            // return view('course.score-sheet.print_chance3_with_scores', compact('course', 'request','MIN_SCORE_FOR_PASSED_EXAM','scores'));
            $pdf = \PDF::loadView('course.score-sheet.print_chance3_with_scores', compact('course', 'request','MIN_SCORE_FOR_PASSED_EXAM','scores'), [], [
                //'format' => 'A4-L',
                'direction' => 'rlt'
            ]);

        } 
        else{
            // return view('course.score-sheet.print_chance3', compact('course', 'request','MIN_SCORE_FOR_PASSED_EXAM','scores'));
            $pdf = \PDF::loadView('course.score-sheet.print_chance3', compact('course', 'request','MIN_SCORE_FOR_PASSED_EXAM','scores'), [], [
                //'format' => 'A4-L',
                'direction' => 'rlt'
            ]);
        }   
        
        return $pdf->stream($course->code.'-print_chance3'.'.pdf');
    }  

    public function print_chance4(Request $request, $course)
    {   
        $MIN_SCORE_FOR_PASSED_EXAM=SystemVariable::where('name','MIN_SCORE_FOR_PASSED_EXAM')->first()->user_value;

        $scores=Score::with('course')->with('student')
        ->where('course_id',$course->id)
        ->where('chance_three','<',$MIN_SCORE_FOR_PASSED_EXAM)
        ->get();     

        if($request->withScores == 1)
        {
            // return view('course.score-sheet.print_chance4_with_scores', compact('course', 'request','MIN_SCORE_FOR_PASSED_EXAM','scores'));
            $pdf = \PDF::loadView('course.score-sheet.print_chance4_with_scores', compact('course', 'request','MIN_SCORE_FOR_PASSED_EXAM','scores'), [], [
                //'format' => 'A4-L',
                'direction' => 'rlt'
            ]);

        } 
        else{
            // return view('course.score-sheet.print_chance4', compact('course', 'request','MIN_SCORE_FOR_PASSED_EXAM','scores'));
            $pdf = \PDF::loadView('course.score-sheet.print_chance4', compact('course', 'request','MIN_SCORE_FOR_PASSED_EXAM','scores'), [], [
                //'format' => 'A4-L',
                'direction' => 'rlt'
            ]);
        }   
        
        return $pdf->stream($course->code.'-print_chance4'.'.pdf');
    }  
}
