<?php

namespace App\Models;

use App\Traits\UseByUniversity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\Downloadble;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Dropout extends Model
{
    use SoftDeletes, UseByUniversity, Downloadble;
    use LogsActivity;
    protected $guarded = [];
    /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'dropouts';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return   " منفکی محصل ". $this->student_id . "  " . trans('general.'. $eventName);
    }
    /** end of log part **/

    public function student()
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id','id');
    }

    public function university()
    {
        return $this->belongsTo(\App\Models\University::class, 'university_id','id');
    }
}
