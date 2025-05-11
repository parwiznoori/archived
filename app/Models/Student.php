<?php

namespace App\Models;

use App\Traits\UseByGrade;
use Illuminate\Support\Facades\Hash;
use App\Traits\Downloadble;
use App\Traits\UseByUniversity;
use App\Traits\UseByDepartment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Yadahan\AuthenticationLog\AuthenticationLogable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    use SoftDeletes, Notifiable, UseByUniversity, UseByDepartment, UseByGrade, AuthenticationLogable, Downloadble, LogsActivity;
    protected $guarded = [];
    protected $role = 'student';
    /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'students';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return   $this->full_name . " ' " . trans('general.'. $eventName);
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

    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }
    //graduated student relation
    public function graduatedStudents()
    {
        return $this->hasOne(GraduatedStudent::class);
    }
    public function updater()
    {
        return $this->belongsTo(\App\User::class, 'updated_by');
    }

    public function university()
    {
        return $this->belongsTo(\App\Models\University::class, 'university_id','id');
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class, 'department_id','id');
    }
    
    public function status()
    {
        return $this->belongsTo(StudentStatus::class);
    }

    public function relatives()
    {
        return $this->hasMany(Relative::class);
    }
    
    public function courses()
    {
        return $this->belongsToMany(Course::class,'course_student','student_id','course_id')->withTimestamps();
    }

    public function originalProvince()
    {
        return $this->belongsTo(\App\Models\Province::class, 'province','id');
    }

    public function currentProvince()
    {
        return $this->belongsTo(Province::class, 'province_current');
    }

    public function photo()
    {
        if (file_exists($this->photo_url)) {
            return asset($this->photo_url);
        } 

        return asset("img/avatar-placeholder.png");
    }

    public function photo_relative_path()
    {
        if (file_exists($this->photo_url)) {
            return $this->photo_url;
        } 
        else 
        {
            return "img/avatar-placeholder.png";
        }
    }

    public function diplomaphoto()
    {
        if (file_exists($this->diploma_photo)) {
            return asset($this->diploma_photo);
        } 
        else {
            if (file_exists($this->photo_url)) {
                return asset($this->photo_url);
            } 
        }
        return asset("img/avatar-placeholder.png");
    }

    public function diplomaphoto_relative_path()
    {
        if (file_exists($this->diploma_photo)) {
            return $this->diploma_photo;
        } 
        else {
            if (file_exists($this->photo_url)) {
                return $this->photo_url;
            } 
        }
        return "img/avatar-placeholder.png";
    }

    public function getFullNameAttribute()
    {
        return $this->name." ".$this->last_name;
    }

    public function semesterScores()
    {
        return $this->hasMany(StudentSemesterScore::class);
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function group_history()
    {
        return $this->belongsToMany(Group::class, 'groups_students_history', 'student_id', 'group_id')->withTimestamps();
    }

    public function transfer()
    {
        return $this->hasMany(Transfer::class, 'student_id', 'id');
    }

    public function dropout()
    {
        return $this->hasMany(Dropout::class, 'student_id', 'id');
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class, 'student_id', 'id');
    }

    public function monograph()
    {
        return $this->hasOne(Monograph::class, 'student_id', 'id');
    }

    public function incrementSemester(){

        $this->increment('semester');
    }

    public function studentResult(){

        $this->hasMany(StudentResult::class);
    }

    public function noticeboardVisits()
    {
        return $this->morphMany(NoticeboardVisit::class, 'visitable');
    }

    public function SemesterDeprived()
    {
        return $this->hasMany(SemesterDeprivedStudent::class);
    }
}
