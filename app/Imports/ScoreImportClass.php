<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Course;
use App\Models\Score;


class ScoreImportClass implements ToCollection
{
    public function collection(Collection $rows)
    {
        $rowIndex = 0;
        $courseId = 0;
        $subjectId = 0;
        $semester = 0;
        
        foreach ($rows as $row) {
            $rowIndex ++;
            $row      = $row->toArray();
            if($rowIndex == 1 || $rowIndex == 3 || $rowIndex == 4 || $rowIndex == 5 ) 
            {
                echo "skip -- no data  <br>";

            }
            else if($rowIndex == 2)
            {
                $courseId =  $row[0];
                
                $course = Course::find($courseId);
                $subjectId = $course->subject_id;
                $semester = $course->semester;


                echo "course : $courseId --- subject : $subjectId <br>";

            }
            else {
                echo "$rowIndex --- st ID :  $row[1] --- course : $courseId --- subject : $subjectId --- semester : $semester <br>";
                $studentId = $row[1];
                $classwork = floatval( $row[7] ?? 0);
                $homework = floatval( $row[8] ?? 0);
                $midterm = floatval( $row[9] ?? 0);
                $final = floatval( $row[10] ?? 0);
                $total = floatval($homework + $classwork + $midterm + $final);
                $present = intval( $row[11] ?? 0);
                $absent = intval( $row[12] ?? 0);
                $absent_exam = intval( $row[13] ?? 0);
                $deprived = intval( $row[14] ?? 0);
                $semester_deprived = intval( $row[15] ?? 0);

                if($semester_deprived == 0)
                {
                    if($absent_exam == 1 || $deprived == 1)
                    {
                        $data = [
                            'student_id' =>  $studentId, 
                            'course_id' => $courseId,       
                            'subject_id' => $subjectId,
                            'semester' => $semester, 
                            'present' => $present,            
                            'absent' => $absent,            
                            'homework' => null,
                            'classwork' => null,
                            'midterm' => null,
                            'final' => null, 
                            'deprived' => $deprived,
                            'absent_exam' => $absent_exam,          
                        ];

                    }
                    else{
                        $data = [
                            'student_id' =>  $studentId, 
                            'course_id' => $courseId,       
                            'subject_id' => $subjectId,
                            'semester' => $semester, 
                            'present' => $present,            
                            'absent' => $absent,            
                            'homework' => $homework,
                            'classwork' => $classwork,
                            'midterm' => $midterm ,
                            'final' => $final,           
                        ];
                    }
                   
                    if($total <= 100 && $final <= 100 && $midterm <= 100 && $classwork <= 100 && $homework <= 100)
                    {
                        $score = Score::where('course_id',$courseId)
                        ->where('student_id',$studentId)->first();
    
                        if ( $score ) {
                            $score->update($data);
                                                            
                        } else {
                            $score = Score::create($data);                
                        } 

                    }

                }
                
            }
    
            
        }
    }
    
}
