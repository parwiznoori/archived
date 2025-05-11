<?php

namespace App\Models;

use App\Traits\UseByUniversity;
use App\Traits\UseByFaculty;
use App\Traits\UseByDepartment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class ArchiveDepartment extends Model
{




    protected $table = 'archive_departments';
    protected $fillable = ['archive_id','university_id', 'faculty_id', 'department_id'];

    public $timestamps = false;


    public function archive()
    {
        return $this->belongsTo(Archive::class,);
    }

    public function archivedata()
    {
        return $this->belongsTo(Archivedata::class,);
    }

    public function university()
    {
        return $this->belongsTo(University::class);
    }


    public function faculty()
    {
        return $this->belongsTo(Faculty::class,);
    }
    public function department()
    {
        return $this->belongsTo(Department::class,);
    }




}