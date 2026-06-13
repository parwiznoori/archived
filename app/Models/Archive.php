<?php

namespace App\Models;

use App\Traits\UseByUniversity;
use App\Traits\UseByFaculty;
use App\Traits\UseByDepartment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Archive extends Model
{
    use SoftDeletes, UseByUniversity, UseByFaculty, UseByDepartment, LogsActivity;

    protected $table = 'archives';
    protected $fillable = [
        'university_id',
        'archive_year_id',
        'book_pagenumber',
        'book_description',
        'book_name',
        'status_id',
        'qc_status_id',
        'de_user_id',
        'qc_user_id'
    ];

    protected $guarded = [];
    protected static $logUnguarded = true;
    protected $dates = ['deleted_at'];

    

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function images()
    {
        return $this->hasMany(Archiveimage::class);
    }

    public function archivedatastatus()
    {
        return $this->belongsTo(Archivedatastatus::class);
    }

    public function archiveqcstatus()
    {
        return $this->belongsTo(Archiveqcstatus::class);
    }

    public function archiveyear()
    {
        return $this->belongsTo(ArchiveYear::class);
    }

    public function archiverole()
    {
        return $this->belongsTo(ArchiveRole::class);
    }

    public function archiveRoles()
{
    return $this->hasMany(\App\Models\ArchiveRole::class, 'archive_id');
}

     public function archiveYears()
    {
        return $this->belongsToMany(ArchiveYear::class, 'archive_archive_year');
    }

    public function shiftTimes()
    {
        return $this->belongsToMany(ShiftTime::class, 'archive_shift_time');
    }

     public function semester_type()
    {
        return $this->belongsTo(SemesterType::class);
    }

    public function semesterTypes()
    {
        return $this->belongsToMany(SemesterType::class, 'archive_semester_type');
    }

    public function archiveDepartments()
{
    return $this->hasMany(ArchiveDepartment::class, 'archive_id', 'id');
}
}
