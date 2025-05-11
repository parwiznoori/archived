<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupStudentHistory extends Model
{
    use SoftDeletes, LogsActivity;

    protected $guarded = [];
   /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'groupStudentHistory';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return  " محصل ".$this->student_id." در گروه ". $this->group_id . " " . trans('general.'. $eventName);
    }
    /** end of log part **/
    protected $table = 'groups_students_history';

    public function students()
    {
        return $this->belongsTo(\App\Models\Student::class,'student_id','id');
    }

    public function groups()
    {
        return $this->belongsTo(\App\Models\Group::class,'group_id','id');
    }  
    
    
}
