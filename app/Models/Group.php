<?php
namespace App\Models;

use App\Traits\UseByUniversity;
use App\Traits\UseByDepartment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes, UseByUniversity, UseByDepartment, LogsActivity;
    protected $guarded = [];
    /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'groups';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return  trans('general.group') . " "  .  trans('general.department')   . " ' " . $this->department->name . " ' " . trans('general.'. $eventName);
    }
    /** end of log part **/

    public function students()
    {
        return $this->hasMany(\App\Models\Student::class)->orderBy('kankor_year','desc')->orderBy('name');
    }

    public function group_history()
    {
        return $this->belongsToMany(Student::class, 'groups_students_history', 'group_id','student_id')->withTimestamps();
    }

    public function courses()
    {
        return $this->belongsToMany('App\Models\Course','course_group','group_id','course_id');
    }  

    public function university()
    {
        return $this->belongsTO(\App\Models\University::class);
    } 

    public function department()
    {
        return $this->belongsTO(\App\Models\Department::class);
    } 

    public function loadStudents()
    {
        return $this->load('students');
    }
    
}
