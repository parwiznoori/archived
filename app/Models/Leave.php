<?php

namespace App\Models;

use App\Traits\UseByUniversity;
use App\Traits\Downloadble;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Leave extends Model
{
    use SoftDeletes, UseByUniversity, Downloadble;
    use LogsActivity;
    protected $guarded = [];
    /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'leaves';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return  " تاحیلی محصل  ". $this->student_id . " " . trans('general.'. $eventName);
    }
    /** end of log part **/

    public function student()
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }

    public function university()
    {
        return $this->belongsTo(\App\Models\University::class, 'university_id','id');
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class, 'department_id','id');
    }

    public function semesterType()
    {
        return $this->belongsTo(\App\Models\SemesterType::class, 'semester_type_id','id');
    }

    public function approved (){
        
        return $this->approved = true;
    }

}
