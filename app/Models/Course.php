<?php

namespace App\Models;

use App\Traits\UseByUniversity;
use App\Traits\UseByDepartment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Course extends Model
{
    use SoftDeletes, UseByUniversity, UseByDepartment, LogsActivity;

    protected $guarded = [];
   /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'Courses';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return trans('general.with_code') . " ' " . $this->code . " ' " . "" .  trans('general.'. $eventName);
    }
    /** end of log part **/
    protected $dates = ['deleted_at'];
    public $casts = ['groups' => 'array'];

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function groups()
    {
        return $this->belongsToMany('App\Models\Group','course_group','course_id','group_id')->withTimestamps();
    }

    public function groupsName()
    {
        return $this->belongsToMany('App\Models\Group','course_group','course_id','group_id')->withTimestamps();
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class)->orderBy('kankor_year','desc')->orderBy('name');
    }

    public function getGradeAttribute()
    {
        if ($this->semester) {
            return ceil($this->semester/2);
        }
    }

    public function getHalfYearTextAttribute()
    {
        return trans('general.'.$this->half_year);
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }
    public function getStudentScore($student_id)
    {
        return $this->scores()->where('student_id', $student_id)->first();
    }

    public function courseTimes()
    {
        return $this->hasMany(CourseTime::class);
    }

    public function course_status()
    {
        return $this->belongsTo(CourseStatus::class,'course_status_id','id');
    }

    public function times()
    {
        return $this->hasMany(CourseTime::class);
    }

    public function loadStudents()
    {
        return $this->load('students');
    }

    public function loadStudentsAndScores()
    {
        return $this->load(['students' => function ($students) {
            $students->with(['scores' => function ($scores) {
                $scores->courseId($this->id);
            }]);
        }]);
    }

    public function loadStudentsAndScoresAndSemesterDeprived()
    {
        return $this->load(['students' => function ($students) {
            $students->with(['scores' => function ($scores) {
                $scores->courseId($this->id);
            }]);
            $students->with(['SemesterDeprived' => function ($SemesterDeprived) {
                $SemesterDeprived->byYearAndSemesterAndHalfYear($this->year,$this->half_year,$this->semester);
            }]);
        }]);
    }

}