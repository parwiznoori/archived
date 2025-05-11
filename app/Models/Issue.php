<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\Attachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Issue extends Model
{
    use SoftDeletes, Attachable, LogsActivity ;

    protected $table = "issues";
    protected $guarded = [];
    /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'issues';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return  trans('general.issue') . " " . trans('general.'. $eventName);
    }
    /** end of log part **/
    public function date()
    {
        Carbon::setLocale('fa');
        return  Carbon::parse($this->created_at)->diffForHumans();
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\IssueComment::class);
    }

    public function isOwner()
    {
        return auth()->user()->id == $this->user_id;
    }

   

}
    
