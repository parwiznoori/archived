<?php

namespace App\Models;


use App\Traits\UseByGrade;
use App\Traits\UseBySemesterType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class ArchiveDocType extends Model
{



    use UseByGrade, LogsActivity;

    protected $table = 'archive_doc_types';
    protected $fillable = ['archivedata_id','doc_type','doc_number','doc_file','doc_description'];

    protected $guarded = [];
    protected static $logUnguarded = true;
    protected $dates = ['deleted_at'];



    public function archivedata()
    {
        return $this->belongsTo(Archivedata::class,);
    }






}