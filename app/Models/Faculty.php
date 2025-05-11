<?php

namespace App\Models;

use App\Traits\UseByUniversity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Faculty extends Model
{
    use SoftDeletes, UseByUniversity, LogsActivity;

    protected $guarded = [];
   /** 
     * log activity parts needed for model
    **/
    protected static $logUnguarded = true;
    protected static $logName = 'faculties';
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return   $this->name . " " . trans('general.'. $eventName);
    }
    /** end of log part **/
    protected $dates = ['deleted_at'];
    protected $table= "faculties";

   
    public $fillable = [
        'name',
        'university_id',
        'name_en',
        'chairman'
    ];


    public function university(){

        return $this->belongsTo(\App\Models\University::class);
    }

    public function departments(){
        return $this->hasMany(\App\Models\Department::class);
    }

}
