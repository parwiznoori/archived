<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class RoleType extends Model
{
    
    use SoftDeletes,LogsActivity;
    protected $guarded = [];

    /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'roleType';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return   $this->name . " " . trans('general.'. $eventName);
    }
    /** end of log part **/

    public function roles(){

        return $this->hasMany(\App\Models\Role::class);
    }
}
