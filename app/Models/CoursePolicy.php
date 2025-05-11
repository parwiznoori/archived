<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CoursePolicy extends Model
{
    use LogsActivity;
    protected $guarded = []; 
    /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'coursesPolicy';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return   $this->title . " " . trans('general.'. $eventName);
    }
    /** end of log part **/
}
