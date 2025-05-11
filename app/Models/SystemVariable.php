<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
class SystemVariable extends Model
{
    use LogsActivity;
    protected $table = "system_variables";
     /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'systemVariables';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return   $this->name . " " . trans('general.'. $eventName);
    }
    /** end of log part **/

    public $fillable = [
        'name',
        'default_value',
        'user_value',
        'description'
    ];
}
