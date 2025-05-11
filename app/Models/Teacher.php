<?php

namespace App\Models;

use App\Traits\UseByUniversity;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\CausesActivity;

use Yadahan\AuthenticationLog\AuthenticationLogable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Teacher extends Authenticatable
{
    use HasApiTokens ,SoftDeletes, Notifiable, UseByUniversity, CausesActivity, AuthenticationLogable, LogsActivity;

    protected $guarded = [];
    protected $guard = 'teacher';
    protected $role = 'teacher';
     /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'teachers';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return $this->full_name . " " . trans('general.'. $eventName);
    }
    /** end of log part **/

    public function hasRole()
    {
        return $this->role; // not assigned
    }

    protected $dates = ['deleted_at'];
    
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function setPasswordAttribute($value)
    {           
        if ($value != '') {
            $this->attributes['password'] = Hash::make($value);
        }        
    }

    public function teacherAcademic()
    {
        return $this->belongsTo(TeacherAcademicRank::class, 'academic_rank_id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province');
    }
    
    public function getFullNameAttribute()
    {
        return $this->name." ".$this->last_name;
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function department()
    {    
        return $this->belongsTo(Department::class);
    }

    public function noticeboardVisits()
    {
        return $this->morphMany(NoticeboardVisit::class, 'visitable');
    }
    
   
}