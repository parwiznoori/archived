<?php

namespace App;

use app\Models\Role;
use App\Traits\UseByUniversity;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\CausesActivity;
use Yadahan\AuthenticationLog\AuthenticationLogable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes, HasRoles, CausesActivity, AuthenticationLogable,LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    protected $guard_name = 'user';
    protected static $logUnguarded = true;
    protected static $logName = 'users';
    protected static $logOnlyDirty = true;
    protected static $ignoreChangedAttributes = ['remember_token'];

    
    public function getDescriptionForEvent(string $eventName): string
    {
        return   $this->name . "  " . trans('general.'. $eventName);
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $dates = ['deleted_at'];

    protected static function boot()
    {
        parent::boot();
    }

    public function setPasswordAttribute($value)
    {           
        if ($value != '') {
            $this->attributes['password'] = Hash::make($value);
        }        
    }

    public function university()
    {
        return $this->belongsTo(\App\Models\University::class);
    }

    public function universities()
    {
        return $this->belongsToMany(\App\Models\University::class,'university_users','user_id','university_id')->withTimestamps();
    }

    public function departments()
    {
        return $this->belongsToMany(\App\Models\Department::class)->withTimestamps()->withoutGlobalScopes();
    }

    public function grades()
    {
        return $this->belongsToMany(\App\Models\Grade::class)->withTimestamps()->withoutGlobalScopes();
    }

    public function allUniversities()
    {
        return $this->university_id == -1;
    }

    public function rolesNames()
    {
        return $this->belongsToMany(\App\Models\Role::class,'model_has_roles','model_id','role_id');
    }

    public function noticeboardVisits()
    {
        return $this->morphMany(\App\Models\NoticeboardVisit::class, 'visitable');
    }

public function archiveRoles()
{
    return $this->hasMany(\App\Models\ArchiveRole::class, 'user_id');
}
   
// رابطه با کتاب‌هایی که کاربر به عنوان درج کننده ثبت کرده است
public function archives()
{
    return $this->hasMany(\App\Models\Archive::class, 'de_user_id');
}

// رابطه با کتاب‌هایی که کاربر به عنوان کنترول کننده بررسی کرده است
public function archivesAsQc()
{
    return $this->hasMany(\App\Models\Archive::class, 'qc_user_id');
}

// رابطه با archivedatas از طریق books
public function archivedatas()
{
    return $this->hasManyThrough(
        \App\Models\Archivedata::class,
        \App\Models\Archive::class,
        'de_user_id', // Foreign key on archives table
        'archive_id', // Foreign key on archivedatas table
        'id', // Local key on users table
        'id' // Local key on archives table
    );
}

}
