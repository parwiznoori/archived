<?php

namespace App\Models;

use Spatie\Permission\Models\Role as BaseRole;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends BaseRole
{
    use SoftDeletes,LogsActivity;
    protected $guarded = [];
   /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'roles';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return   $this->title . " " . trans('general.'. $eventName);
    }
    /** end of log part **/

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('admin', function ($builder) {
            
            if (!auth()->user()->allUniversities()) {
                
                $builder->where('admin', 0);
            }
        });
    }
    public function roleType(){
        return $this->belongsTo(\App\Models\RoleType::class,'type_id','id');
    }
}
