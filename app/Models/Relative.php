<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;

class Relative extends Model
{
    use LogsActivity;
    public $timestamps = false;
    protected $guarded = [];
    /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'relatives';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return   "  اقارب محصل " .$this->student_id . " " . trans('general.'. $eventName);
    }
    /** end of log part **/
    

    public function students()
    {
        return $this->belongsTo(\App\Models\Student::class);
    }
}
