<?php

namespace App\Models;
use Carbon\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Model;

class CourseTime extends Model
{

    use LogsActivity;
    protected $guarded = []; 
   /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'courseTime';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return trans('general.time') . $this->time . "" .  trans('general.'. $eventName);
    }
    /** end of log part **/

    public function day()
    {
        return $this->belongsTo(Day::class);
    }

     public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // public function time()
    // {   
    //     Carbon::setLocale('fa');
    //     return  Carbon::parse($this->time)->diffForHumans();
    // }

   
}
