<?php

namespace App\Models;

use App\Traits\UseByGrade;
use Illuminate\Support\Facades\Hash;
use App\Traits\Downloadble;
use App\Traits\UseByUniversity;
use App\Traits\UseByDepartment;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;

use Spatie\Activitylog\Traits\LogsActivity;
use Yadahan\AuthenticationLog\AuthenticationLogable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class GraduateBooksPdf extends Authenticatable
{
    use  Notifiable, UseByUniversity, UseByDepartment, UseByGrade, AuthenticationLogable, Downloadble, LogsActivity;
    
    protected $guarded = [];
   /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'graduateBooksPdf';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return   "  کتاب فارغان دپارتمنت".$this->department->name . "(". $this->university->name .")" . " سال فراغت " .$this->graduated_year. ' ' .trans('general.'. $eventName);
    }
    /** end of log part **/
    protected $table = "graduate_books_pdf";

    public $fillable = [
        'university_id','department_id','grade_id','graduated_year','status','fileName','user_id','generated_count'
    ];

    public function university()
    {
        return $this->belongsTo(University::class);
    }
    
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
    
    public function user()
    {
        return $this->belongsTo(\App\User::class)->withoutGlobalScopes();
    }
   
}
