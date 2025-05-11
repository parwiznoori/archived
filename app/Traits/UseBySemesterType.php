<?php 

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

trait UseBySemesterType
{
    protected static function bootUseBySemesterType()
    {
        static::addGlobalScope('semester_type', function ($query) {

            //if user assigned to semester_type filter else not filter
            if (auth('user')->check() and auth()->user()->semester_type->count()) {
                
                $query->whereIn($query->getQuery()->from . '.semester_type_id',  auth()->user()->semester_type->pluck('id'));
   
            }

        });

    }

    public function scopeAllSemesterTypes($query)
    {
        return $query->withoutGlobalScope('semester_type');
    }


    public function semester_type()
    {
        return $this->belongsTo(\App\Models\SemesterType::class);
    }
}
