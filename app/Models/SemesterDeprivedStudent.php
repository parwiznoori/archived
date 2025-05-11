<?php

namespace App\Models;

use App\Traits\UseByUniversity;
use App\Traits\UseByDepartment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;


class SemesterDeprivedStudent extends Model
{
    use SoftDeletes, UseByUniversity, UseByDepartment, LogsActivity;
    
    protected $guarded = [];
    protected $table = 'semester_deprived_students';
    
   /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'SemesterDeprivedStudents';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return   " محصل محروم سمستر ".$this->student->full_name . "  " . trans('general.'. $eventName);
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

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function scopeByYearAndSemesterAndHalfYear($query, $year,$half_year,$semester)
    {
        return $query->where('educational_year', $year)
                    ->where('semester', $semester)
                    ->where('half_year', $half_year);
    }

}
