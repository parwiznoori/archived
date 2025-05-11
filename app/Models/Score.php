<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Score extends Model
{
    use SoftDeletes, LogsActivity;

    protected $guarded = [];
    /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'roles';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return trans("general.score_of_subject_for_student_was_action", [            
                'subject' => $this->subject->title,
                'student' => $this->student->code." ".$this->student->full_name,
                'action' => trans('general.'.$eventName)            
            ]);
    }
    /** end of log part **/

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if(isset($model->final) && $model->final >= 0)
            {
                $model->total = $model->classwork + $model->homework + $model->midterm + $model->final;

            }
            else{
                $model->total =null;
            }
            

            if ($model->chance_four != "" and $model->chance_four >= 55) {
                $model->passed = 1;
            } elseif ($model->chance_three != "" and $model->chance_three >= 55) {
                $model->passed = 1;
            } elseif ($model->chance_two != "" and $model->chance_two >= 55) {
                $model->passed = 1;
            } elseif ($model->total >= 55) {
                $model->passed = 1;
            } else {
                $model->passed = 0;
            }            
        });

        static::updating(function ($model) {

            if(isset($model->final) && $model->final >= 0)
            {
                $model->total = $model->classwork + $model->homework + $model->midterm + $model->final;

            }
            else{
                $model->total =null;
            }

            
            if ($model->chance_four != "" and $model->chance_four >= 55) {
                $model->passed = 1;
            } elseif ($model->chance_three != "" and $model->chance_three >= 55) {
                $model->passed = 1;
            } elseif ($model->chance_two != "" and $model->chance_two >= 55) {
                $model->passed = 1;
            } elseif ($model->total >= 55) {
                $model->passed = 1;
            } else {
                $model->passed = 0;
            }            
        });
    }

    public function scopeCourseId($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
    
   

    public function validForChanceTwo($min_score=null)
    {
        $min_score_exam=55;
        if($min_score)
        {
            $min_score_exam=$min_score;
        }
        $valid_for_chance_two=false;
        // if student was absent in final exam or was deprived => student will be present in chance two
        if($this->deprived == 1 || ($this->absent_exam == 1 and $this->total < $min_score_exam ) )
        {
            $valid_for_chance_two=true;

        }
        if( $this->total !== null and $this->total < $min_score_exam)
        {
            $valid_for_chance_two=true;
        }
        return  $valid_for_chance_two;   
  
              
    }
    
    public function validForChanceThree($min_score=null)
    {
        $min_score_exam=55;
        $valid_for_chance_three=false;
        if($min_score)
        {
            $min_score_exam=$min_score;
        }

        if( $this->chance_two !== null and $this->chance_two < $min_score_exam)
        {
            $valid_for_chance_three=true;
        }

        return $valid_for_chance_three;
                       
    }
    
    public function validForChanceFour($min_score=null)
    {
        $min_score_exam=55;
        $valid_for_chance_four=false;
        if($min_score)
        {
            $min_score_exam=$min_score;
        }

        if( $this->chance_three !== null and $this->chance_three < $min_score_exam)
        {
            $valid_for_chance_four=true;
        }

        return $valid_for_chance_four;
                   
    } 
    
    public function isDeprived(){
        $is_deprived=false;
        if(($this->present * 25) / 100 < $this->absent)
        {
            $is_deprived=true;

        }
        if($this->deprived == 1)
        {
            $is_deprived=true;
        }

        return $is_deprived;
    }

    public function course_status()
    {
        return $this->belongsTo(CourseStatus::class, 'course_status_id' ,'id');
    }
}
