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
class GraduatedStudent extends Authenticatable
{
    use SoftDeletes, Notifiable, UseByUniversity, UseByDepartment, UseByGrade, AuthenticationLogable, Downloadble, LogsActivity;
    
    protected $guarded = [];
   /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'graduatedStudents';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return   " محصل فارغ التحصیل ".$this->student->full_name . "  " . trans('general.'. $eventName);
    }
    /** end of log part **/

    protected $dates = ['deleted_at'];

   
    
    public function setRegisterationDateAttribute($value)
    {
        if($value)
        {
            $registeration_date=explode('/',$value);
            $date1=\Morilog\Jalali\CalendarUtils::toGregorian($registeration_date[0],$registeration_date[1], $registeration_date[2]);
            $registeration_date_gregorian=implode('-',$date1);
            $this->attributes['registeration_date'] = strtolower($registeration_date_gregorian);
        }   
    }

    public function getRegisterationDateAttribute($value)
    {
        if($value)
        {
            $jalali_date=explode('-',$value);
            $jDate = \Morilog\Jalali\CalendarUtils::toJalali($jalali_date[0],$jalali_date[1],$jalali_date[2]);
            $registeration_date=implode('/',$jDate);
            return $registeration_date;
        }
        else{
            return '';
        }
    }

    public function setReceivedDiplomaDateAttribute($value)
    {
        if($value)
        {
            $received_diploma_date=explode('/',$value);
            $date1=\Morilog\Jalali\CalendarUtils::toGregorian($received_diploma_date[0],$received_diploma_date[1], $received_diploma_date[2]);
            $received_diploma_date_gregorian=implode('-',$date1);
            $this->attributes['received_diploma_date'] = strtolower($received_diploma_date_gregorian);
        }   
    }

    public function getReceivedDiplomaDateAttribute($value)
    {
        if($value)
        {
            $jalali_date=explode('-',$value);
            $jDate = \Morilog\Jalali\CalendarUtils::toJalali($jalali_date[0],$jalali_date[1],$jalali_date[2]);
            $received_diploma_date=implode('/',$jDate);
            return $received_diploma_date;
        }
        else{
            return '';
        }
    }

    public function setDiplomaLetterDateAttribute($value)
    {
        if($value)
        {
            $diploma_letter_date=explode('/',$value);
            $date1=\Morilog\Jalali\CalendarUtils::toGregorian($diploma_letter_date[0],$diploma_letter_date[1], $diploma_letter_date[2]);
            $diploma_letter_date_gregorian=implode('-',$date1);
            $this->attributes['diploma_letter_date'] = strtolower($diploma_letter_date_gregorian);
        }   
    }

    public function getDiplomaLetterDateAttribute($value)
    {
        if($value)
        {
            $jalali_date=explode('-',$value);
            $jDate = \Morilog\Jalali\CalendarUtils::toJalali($jalali_date[0],$jalali_date[1],$jalali_date[2]);
            $diploma_letter_date=implode('/',$jDate);
            return $diploma_letter_date;
        }
        else{
            return '';
        }
    }

    public function setReceivedCertificateDateAttribute($value)
    {
        if($value)
        {
            $received_certificate_date=explode('/',$value);
            $date1=\Morilog\Jalali\CalendarUtils::toGregorian($received_certificate_date[0],$received_certificate_date[1], $received_certificate_date[2]);
            $received_certificate_date_gregorian=implode('-',$date1);
            $this->attributes['received_certificate_date'] = strtolower($received_certificate_date_gregorian);
        }   
    }

    public function getReceivedCertificateDateAttribute($value)
    {
        if($value)
        {
            $jalali_date=explode('-',$value);
            $jDate = \Morilog\Jalali\CalendarUtils::toJalali($jalali_date[0],$jalali_date[1],$jalali_date[2]);
            $received_certificate_date=implode('/',$jDate);
            return $received_certificate_date;
        }
        else{
            return '';
        }
    }

    public function setCertificateLetterDateAttribute($value)
    {
        if($value)
        {
            $certificate_letter_date=explode('/',$value);
            $date1=\Morilog\Jalali\CalendarUtils::toGregorian($certificate_letter_date[0],$certificate_letter_date[1], $certificate_letter_date[2]);
            $certificate_letter_date_gregorian=implode('-',$date1);
            $this->attributes['certificate_letter_date'] = strtolower($certificate_letter_date_gregorian);
        }   
    }

    public function getCertificateLetterDateAttribute($value)
    {
        if($value)
        {
            $jalali_date=explode('-',$value);
            $jDate = \Morilog\Jalali\CalendarUtils::toJalali($jalali_date[0],$jalali_date[1],$jalali_date[2]);
            $certificate_letter_date=implode('/',$jDate);
            return $certificate_letter_date;
        }
        else{
            return '';
        }
    }

    public function setReceivedTranscriptEnDateAttribute($value)
    {
        if($value)
        {
            $received_transcript_en_date=explode('/',$value);
            $date1=\Morilog\Jalali\CalendarUtils::toGregorian($received_transcript_en_date[0],$received_transcript_en_date[1], $received_transcript_en_date[2]);
            $received_transcript_en_date_gregorian=implode('-',$date1);
            $this->attributes['received_transcript_en_date'] = strtolower($received_transcript_en_date_gregorian);
        }   
    }

    public function getReceivedTranscriptEnDateAttribute($value)
    {
        if($value)
        {
            $jalali_date=explode('-',$value);
            $jDate = \Morilog\Jalali\CalendarUtils::toJalali($jalali_date[0],$jalali_date[1],$jalali_date[2]);
            $received_transcript_en_date=implode('/',$jDate);
            return $received_transcript_en_date;
        }
        else{
            return '';
        }
    }

    public function setTranscriptEnLetterDateAttribute($value)
    {
        if($value)
        {
            $transcript_en_letter_date=explode('/',$value);
            $date1=\Morilog\Jalali\CalendarUtils::toGregorian($transcript_en_letter_date[0],$transcript_en_letter_date[1], $transcript_en_letter_date[2]);
            $received_transcript_en_date_gregorian=implode('-',$date1);
            $this->attributes['transcript_en_letter_date'] = strtolower($received_transcript_en_date_gregorian);
        }   
    }

    public function getTranscriptEnLetterDateAttribute($value)
    {
        if($value)
        {
            $jalali_date=explode('-',$value);
            $jDate = \Morilog\Jalali\CalendarUtils::toJalali($jalali_date[0],$jalali_date[1],$jalali_date[2]);
            $transcript_en_letter_date=implode('/',$jDate);
            return $transcript_en_letter_date;
        }
        else{
            return '';
        }
    }

    public function setReceivedTranscriptDaDateAttribute($value)
    {
        if($value)
        {
            $received_transcript_da_date=explode('/',$value);
            $date1=\Morilog\Jalali\CalendarUtils::toGregorian($received_transcript_da_date[0],$received_transcript_da_date[1], $received_transcript_da_date[2]);
            $received_transcript_da_date_gregorian=implode('-',$date1);
            $this->attributes['received_transcript_da_date'] = strtolower($received_transcript_da_date_gregorian);
        }   
    }

    public function getReceivedTranscriptDaDateAttribute($value)
    {
        if($value)
        {
            $jalali_date=explode('-',$value);
            $jDate = \Morilog\Jalali\CalendarUtils::toJalali($jalali_date[0],$jalali_date[1],$jalali_date[2]);
            $received_transcript_da_date=implode('/',$jDate);
            return $received_transcript_da_date;
        }
        else{
            return '';
        }
    }

    public function setTranscriptDaLetterDateAttribute($value)
    {
        if($value)
        {
            $transcript_da_letter_date=explode('/',$value);
            $date1=\Morilog\Jalali\CalendarUtils::toGregorian($transcript_da_letter_date[0],$transcript_da_letter_date[1], $transcript_da_letter_date[2]);
            $transcript_da_letter_date_gregorian=implode('-',$date1);
            $this->attributes['transcript_da_letter_date'] = strtolower($transcript_da_letter_date_gregorian);
        }   
    }

    public function getTranscriptDaLetterDateAttribute($value)
    {
        if($value)
        {
            $jalali_date=explode('-',$value);
            $jDate = \Morilog\Jalali\CalendarUtils::toJalali($jalali_date[0],$jalali_date[1],$jalali_date[2]);
            $transcript_da_letter_date=implode('/',$jDate);
            return $transcript_da_letter_date;
        }
        else{
            return '';
        }
    }

    public function setReceivedTranscriptPaDateAttribute($value)
    {
        if($value)
        {
            $received_transcript_pa_date=explode('/',$value);
            $date1=\Morilog\Jalali\CalendarUtils::toGregorian($received_transcript_pa_date[0],$received_transcript_pa_date[1], $received_transcript_pa_date[2]);
            $received_transcript_pa_date_gregorian=implode('-',$date1);
            $this->attributes['received_transcript_pa_date'] = strtolower($received_transcript_pa_date_gregorian);
        }   
    }

    public function getReceivedTranscriptPaDateAttribute($value)
    {
        if($value)
        {
            $jalali_date=explode('-',$value);
            $jDate = \Morilog\Jalali\CalendarUtils::toJalali($jalali_date[0],$jalali_date[1],$jalali_date[2]);
            $received_transcript_pa_date=implode('/',$jDate);
            return $received_transcript_pa_date;
        }
        else{
            return '';
        }
    }

    public function setTranscriptPaLetterDateAttribute($value)
    {
        if($value)
        {
            $transcript_pa_letter_date=explode('/',$value);
            $date1=\Morilog\Jalali\CalendarUtils::toGregorian($transcript_pa_letter_date[0],$transcript_pa_letter_date[1], $transcript_pa_letter_date[2]);
            $transcript_pa_letter_date_gregorian=implode('-',$date1);
            $this->attributes['transcript_pa_letter_date'] = strtolower($transcript_pa_letter_date_gregorian);
        }   
    }

    public function getTranscriptPaLetterDateAttribute($value)
    {
        if($value)
        {
            $jalali_date=explode('-',$value);
            $jDate = \Morilog\Jalali\CalendarUtils::toJalali($jalali_date[0],$jalali_date[1],$jalali_date[2]);
            $transcript_pa_letter_date=implode('/',$jDate);
            return $transcript_pa_letter_date;
        }
        else{
            return '';
        }
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
    
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
    
   
}
