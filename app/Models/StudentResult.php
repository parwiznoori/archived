<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class StudentResult extends Model
{
    use LogsActivity;
    protected $guarded = [];
    /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'studentResults';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return  " نتیجه سمستر محصل  ". $this->student_id . " " . trans('general.'. $eventName);
    }
    /** end of log part **/


    public static function addNewStatusResult($student_id, $kankor_year, $semester, $department_id, $education_year,$isPassed, $grade, $averageScore,$increaseSemester,$totalCredit,$numberOfPassedCredits){

        $student = StudentResult::where('student_id', $student_id)
                                ->where('year', $kankor_year)
                                ->where('semester',$semester)
                                ->where('education_year',$education_year)
                                ->first();

        if(!$student){
            $sResult = new StudentResult();
            $sResult->student_id = $student_id;
            $sResult->year = $kankor_year;
            $sResult->semester = $semester;
            $sResult->department_id = $department_id;
            $sResult->education_year = $education_year;
            $sResult->isPassed = $isPassed;
            $sResult->grade = $grade;
            $sResult->result = $averageScore;
            $sResult->increase_semester = $increaseSemester;
            $sResult->semester_credits = $totalCredit;
            $sResult->success_credits = $numberOfPassedCredits;
            $sResult->save();
            return true;
        }
        else{
            if(
                $student->result == $averageScore && 
                $student->grade == $grade &&
                $student->isPassed == $isPassed &&
                $student->increase_semester == $increaseSemester &&
                $student->semester_credits == $totalCredit &&
                $student->success_credits == $numberOfPassedCredits &&
                $student->education_year == $education_year
            )
            {
                return false;
            }
            else{
                $student->result = $averageScore ;
                $student->grade = $grade ;
                $student->isPassed = $isPassed ;
                $student->increase_semester = $increaseSemester ;
                $student->semester_credits = $totalCredit ;
                $student->success_credits = $numberOfPassedCredits;
                $student->education_year = $education_year ;
                $student->save();
                return true;
            }
           
        }
    }

    public function student(){

       return $this->belongsTo(Student::class);
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class, 'department_id','id');
    }
    
}
