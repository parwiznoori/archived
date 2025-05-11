<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\UseByUniversity;
use App\Traits\UseByDepartment;
use App\Traits\UseByGrade;

class StudentSemesterScore extends Model
{
    use SoftDeletes,LogsActivity;      // ,UseByUniversity, UseByDepartment, UseByGrade;

    protected $guarded = [];
    protected $table='students_semester_scores';
     /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'studentsSemesterScores';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return  " در جدول نتایج - نمره محصل  ". $this->student_id . "  در مضمون ".$this->subject_id . trans('general.'. $eventName);
    }
    /** end of log part **/

    protected $dates = ['deleted_at'];


    public static function addSubjectScore($student_id,$subject_id,$education_year, $semester, $course_id, $score_id, $chance_one , $chance_two ,$chance_three ,$chance_four ,$success_score ,$success_chance)
    {
        $student = StudentSemesterScore::where('student_id', $student_id)
                                ->where('subject_id', $subject_id)
                                ->first();

        if(!$student){
            $sResult = new StudentSemesterScore();
            $sResult->student_id = $student_id;
            $sResult->subject_id = $subject_id;
            $sResult->course_id = $course_id;
            $sResult->score_id = $score_id;
            $sResult->education_year = $education_year;
            $sResult->semester = $semester;
            $sResult->chance_one = $chance_one;
            $sResult->chance_two = $chance_two;
            $sResult->chance_three = $chance_three;
            $sResult->chance_four = $chance_four;
            $sResult->success_score = $success_score;
            $sResult->success_chance = $success_chance;
            $sResult->save();
            return true;
        }
        else{
            return false;
        }
    }

    public function student(){
       return $this->belongsTo(Student::class);
    }

    public function subject(){
        return $this->belongsTo(Subject::class);
    }

    public function course(){
        return $this->belongsTo(Course::class);
    }

    public function score(){
        return $this->belongsTo(Score::class);
    }
    
}
