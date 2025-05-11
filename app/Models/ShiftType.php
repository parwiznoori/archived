<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class ShiftType extends Model
{
    
    use LogsActivity;
    protected $guarded = [];
    protected $table = "shift_types";
    /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'shiftTypes';
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
