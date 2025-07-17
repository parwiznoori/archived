<?php 

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

trait UseByUniversity
{
    protected static function bootUseByUniversity()
    {
        static::addGlobalScope('university', function ($query) {
            if(auth('user')->check())
            {
                $user_type = auth()->user()->user_type;
            }
            else
            {
                 $user_type = null;
            }
            
            if (!auth()->guest() and !auth()->user()->allUniversities() and ($user_type == 2 || $user_type == 3 )) {
               $query->whereIn($query->getQuery()->from . '.university_id', auth()->user()->universities->pluck('id'));
            }

        });

        static::saving(function (Model $model) {
            if (!isset($model->university_id) and auth()->user()->user_type == 3) {
                $model->university_id = auth()->user()->universities->first()->id;
            }
        });
    }

    public function scopeAllUniversities($query)
    {
        return $query->withoutGlobalScope('university');
    }


    public function university()
    {
        return $this->belongsTo(\App\Models\University::class);
    }

    public function universities()
    {
        return $this->belongsToMany(\App\Models\University::class,'university_users','user_id','university_id')->withTimestamps();
    }
}
