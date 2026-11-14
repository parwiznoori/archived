<?php

namespace App\Models;


use App\Traits\UseByGrade;
use App\Traits\UseBySemesterType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class ArchiveFormPrint extends Model
{



    use UseByGrade, LogsActivity;

    protected $table = 'archive_form_prints';
    protected $fillable = ['archive_form_temp_id', 'content', 'archivedata_id'];

    protected $guarded = [];
    protected static $logUnguarded = true;
    protected $casts = ['deleted_at' => 'datetime'];



    public function archivedata()
    {
        return $this->belongsTo(Archivedata::class,);
    }

    public function archiveformtemplate()
    {
        return $this->belongsTo(ArchiveFormTemplate::class,);
    }







}