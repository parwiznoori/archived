<?php

namespace App\Models;

use App\Traits\UseByUniversity;
use App\Traits\UseByDepartment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Monograph extends Model
{
    use SoftDeletes, UseByUniversity, UseByDepartment, LogsActivity;

    protected $table = 'monographs';
    protected $fillable = ['university_id','department_id','student_id','teacher_id','title','defense_date','score'];

    protected $guarded = [];
    /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'monographs';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return  " مونوگراف ". $this->title . " " . trans('general.'. $eventName);
    }
    /** end of log part **/
    protected $dates = ['deleted_at'];

    public function getDefenseDateAttribute($value)
    {
        $jalali_date=explode('-',$value);

        $jDate = \Morilog\Jalali\CalendarUtils::toJalali($jalali_date[0],$jalali_date[1],$jalali_date[2]);
        $defense_date=implode('/',$jDate);
        return $defense_date;
    }
    public function setDefenseDateAttribute($value)
    {
        $defense_date=explode('/',$value);
        $BirthDate_g1=\Morilog\Jalali\CalendarUtils::toGregorian($defense_date[0],$defense_date[1], $defense_date[2]);
        $defense_date_gregorain=implode('-',$BirthDate_g1);
        $this->attributes['defense_date'] = strtolower($defense_date_gregorain);
       
    }

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

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }


}