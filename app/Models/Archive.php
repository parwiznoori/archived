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
}
