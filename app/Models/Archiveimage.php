<?php

namespace App\Models;

use App\Traits\UseByUniversity;
use App\Traits\UseByFaculty;
use App\Traits\UseByDepartment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Archiveimage extends Model
{
    use  LogsActivity;

    protected $table = 'archiveimages';
    protected $fillable = ['archive_id','path','book_pagenumber','total_students','status_id','qc_status_id'];

    protected $guarded = [];
    protected static $logUnguarded = true;
    protected $dates = ['deleted_at'];

   

    public function archive()
    {
        return $this->belongsTo(Archive::class);
    }
    
    public function archivedatastatus()
    {
        return $this->belongsTo(Archivedatastatus::class);
    }
    public function archiveqcstatus()
    {
        return $this->belongsTo(Archiveqcstatus::class);
    }

}