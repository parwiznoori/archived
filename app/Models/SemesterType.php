<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class SemesterType extends Model
{
    use  LogsActivity;

    protected $table = 'semester_type';
    protected $fillable = ['name','name_en'];

    protected $guarded = [];
   /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'semesterTypes';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return   $this->name . " " . trans('general.'. $eventName);
    }
    /** end of log part **/

}