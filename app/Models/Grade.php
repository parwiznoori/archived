<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Grade extends Model
{
    //grade model
    use LogsActivity;
    use SoftDeletes;
    protected $guarded = [];
    protected $table = "grades";
    protected $dates = ['deleted_at'];
    /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'grades';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return   $this->name . " " . trans('general.'. $eventName);
    }
    /** end of log part **/

    public $fillable = [
        'name'
    ];

}
