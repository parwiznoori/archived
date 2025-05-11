<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
class StudentStatus extends Model
{
    
    
    public function students()
    {
        return $this->hasMany(\App\Models\Student::class);
    }

    use LogsActivity;
    protected $guarded = [];
    protected $table = "student_statuses";
     /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'studentStatuses';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return   $this->title . " " . trans('general.'. $eventName);
    }
    /** end of log part **/


    public $fillable = [
        'title', 'tag_color', 'editable',
    ];

}