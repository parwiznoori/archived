<?php

namespace App\Models;


use App\Traits\UseByGrade;
use App\Traits\UseBySemesterType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class ArchiveFormPrint extends Model
{



    use UseByGrade, LogsActivity;

    protected $table = 'archive_form_prints';
    protected $fillable = ['archive_form_temp_id', 'content', 'archivedata_id'];

    protected $guarded = [];
    protected static $logUnguarded = true;
    protected $dates = ['deleted_at'];



    public function archivedata()
    {
        return $this->belongsTo(Archivedata::class,);
    }

    public function archiveformtemplate()
    {
        return $this->belongsTo(ArchiveFormTemplate::class,);
    }







}