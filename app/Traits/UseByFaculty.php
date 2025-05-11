<?php 

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

trait UseByFaculty
{
    protected static function bootUseByFaculty()
    {
        static::addGlobalScope('faculty', function ($query) {

            //if user assigned to faculties filter else not filter
            // if (!auth()->guest() and !auth()->user()->allUniversities() and auth()->user()->faculties->count()) { 
             
            //      $query->whereIn($query->getQuery()->from . '.faculty_id',  auth()->user()->faculties->pluck('id'));
   
            // }

        });

    }
    //facultyfile
    public function scopeAllfaculties($query)
    {
        return $query->withoutGlobalScope('faculty');
    }


    public function faculty()
    {
        return $this->belongsTo(\App\Models\Faculty::class);
    }
}
