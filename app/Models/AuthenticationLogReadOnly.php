<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class AuthenticationLogReadOnly extends Model
{
    protected $guarded = [];
    protected $table = "authentication_log";
    protected $dates = ['login_at'];

}
    /**
     * log activity parts needed for model
    **/
    // protected static $logUnguarded = true;
    // protected static $logName = 'authentication_log';
    // protected static $logOnlyDirty = true;

    // public function getDescriptionForEvent(string $eventName): string
    // {
    //     return   "login_report.index" . " " . trans('general.'. $eventName);
    // }
    /** end of log part **/

    // public $fillable = [
    //     'authenticatable_type',
    //     'authenticatable_id',
    //     'ip_address',
    //     'user_agent',
    //     'login_at',
    //     'logout_at'
    // ];


