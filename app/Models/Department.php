<?php

namespace App\Models;

use App\Traits\UseByUniversity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Department extends Model
{
    use SoftDeletes, UseByUniversity, LogsActivity;

    protected $guarded = [];
    /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'departments';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        if(isset($this->university->name))
            $university_name=$this->university->name ;
        else
            $university_name='';
        return  trans('general.department') . " "  .  trans('general.university')   . " ' " . $university_name . " ' " . trans('general.'. $eventName);
    }
    /** end of log part **/
    protected $dates = ['deleted_at'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('department', function ( $query) {
            
            //if user assigned to departments filter else not filter
            if (! auth()->guest() and ! auth()->user()->allUniversities() and auth()->user()->departments->count()) {
               
                $query->whereIn('departments.id',  auth()->user()->departments->pluck('id'));
   
            }
        });
    }

    public function users()
    {
        return $this->belongsToMany(\App\User::class)->withTimestamps();
    }

    public function university(){

        return $this->belongsTo(\App\Models\University::class);
    }

    public function grade(){

        return $this->belongsTo(\App\Models\Grade::class);
    }

    public function facultyName(){

        return $this->belongsTo(\App\Models\Faculty::class,'faculty_id','id');
    }

    public function departmentType(){

        return $this->belongsTo(\App\Models\DepartmentType::class,'department_type_id','id');
    }

    public function students()
    {
        return $this->hasMany(\App\Models\Student::class);
    }

    public function subjects()
    {
        return $this->hasMany(\App\Models\Subject::class);
    }

    public function courses()
    {
        return $this->hasMany(\App\Models\Course::class);
    }
    public function teachers()
    {
        return $this->hasMany(\App\Models\Teacher::class);
    }

    public function studentsByStatus()
    {
        return $this->students()->select('department_id', 'status_id', \DB::raw('COUNT(students.id) as students_count'))
            ->groupBy('department_id', 'status_id');
    }
    public function group()
    {
        return $this->hasMany(\App\Models\Group::class);
    }

    public function archive()
    {
        return $this->belongsToMany(Archive::class, );
    }




}
