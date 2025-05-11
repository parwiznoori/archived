<?php

namespace App\Models;

use App\Traits\UseByUniversity;
use App\Traits\UseByDepartment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class LessonWeek extends Model
{
    use SoftDeletes, UseByUniversity, UseByDepartment, LogsActivity;

    protected $table = 'lesson_weeks';
    protected $fillable = ['university_id','department_id','education_year','half_year','number_of_weeks'];

    protected $guarded = [];
   /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'lessonWeeks';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        if(isset($this->university->name))
            $university_name=$this->university->name ;
        else
            $university_name='';
        return  " هفته های درسی برای دپارتمنمت  ".trans('general.department') . " "  .  trans('general.university')   . " ' " . $university_name . " ' " . trans('general.'. $eventName);
    }
    /** end of log part **/

    protected $dates = ['deleted_at'];

    public function university()
    {
        return $this->belongsTo(University::class);
    }
   
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

}