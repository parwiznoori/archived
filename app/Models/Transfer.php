<?php

namespace App\Models;
use App\Traits\Downloadble;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Transfer extends Model
{
    use Downloadble;
    use SoftDeletes;
    use  LogsActivity;

    protected $guarded = [];
     /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'transfers';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return  " تبدیلی محصل  ". $this->student_id. " " . trans('general.'. $eventName);
    }
    /** end of log part **/


    public function fromDepartment()
    {
        return $this->belongsTo(\App\Models\Department::class, 'from_department_id')->withoutGlobalScope('department')->allUniversities();
    }

    public function student()
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id')->withoutGlobalScope('department')->allUniversities();
    }

    public function toDepartment(){

        return $this->belongsTo(\App\Models\Department::class, 'to_department_id')->withoutGlobalScope('department')->allUniversities();
    } 

    public function getDepartmentLastStudentMark($year)
    {
        dd($year);

    }
    
    
}