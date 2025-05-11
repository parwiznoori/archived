<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class TeacherAcademicRank extends Model
{
    
   
    use LogsActivity;
    protected $guarded = [];
    protected $table = "teacher_academic_ranks";
     /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'teacherAcademicRanks';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return   $this->title . " " . trans('general.'. $eventName);
    }
    /** end of log part **/

    public $fillable = [
        'title'
    ];

    public function teachers()
    {
        
        return $this->hasMany(\App\Models\Teacher::class);
    }

}