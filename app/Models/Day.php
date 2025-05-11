<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;



class Day extends Model
{
    use LogsActivity;
    protected $guarded = [];
    protected $table = "days";
    /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'days';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return   $this->day . " " . trans('general.'. $eventName);
    }
    /** end of log part **/

    public $fillable = [
        'day'
    ];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}









