<?php

namespace App\Models;

use App\Traits\UseByGrade;
use App\Traits\UseBySemesterType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Morilog\Jalali\Jalalian;
use Spatie\Activitylog\Traits\LogsActivity;

class Archivedata extends Model
{
    use SoftDeletes,UseByGrade, LogsActivity;

    protected $table = 'archivedatas';
    protected $fillable = ['archive_id','archiveimage_id','name','last_name','father_name','grandfather_name',
   'school','school_graduation_year','tazkira_number','birth_date',
    'birth_place','time','kankor_id','semester_type_id','year_of_inclusion','graduated_year','transfer_year',
    'leave_year','failled_year','monograph_date','monograph_number','monograph_title',
     'averageOfScores','grade_id','status_id','qc_status_id',
    'description','column_number','university_id', 'faculty_id', 'department_id','monograph_doc_date','monograph_doc_number'];
         
    protected $guarded = [];
    protected static $logUnguarded = true;
    protected $dates = ['deleted_at'];



//    public function getMonographDateAttribute($value)
//    {
//        $jalali_date=explode('-',$value);
//
//        $jDate = \Morilog\Jalali\CalendarUtils::toJalali($jalali_date[0],$jalali_date[1],$jalali_date[2]);
//        $monograph_date=implode('/',$jDate);
//        return $monograph_date;
//    }
//
//
//
//    public function setMonographDateAttribute($value)
//    {
//        $monograph_date=explode('/',$value);
//        $BirthDate_g1=\Morilog\Jalali\CalendarUtils::toGregorian($monograph_date[0],$monograph_date[1], $monograph_date[2]);
//        $monograph_date_gregorain=implode('-',$BirthDate_g1);
//        $this->attributes['monograph_date'] = strtolower($monograph_date_gregorain);
//
//    }


    public function archiveimage()
    {
        return $this->belongsTo(Archiveimage::class, 'archiveimage_id');
    }

    public function semester_type()
    {
        return $this->belongsTo(SemesterType::class);
    }
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function archivedatastatus()
    {
        return $this->belongsTo(Archivedatastatus::class);
    }

    public function archiveqcstatus()
    {
        return $this->belongsTo(Archiveqcstatus::class);
    }

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class,);
    }
    public function department()
    {
        return $this->belongsTo(Department::class,);
    }




}