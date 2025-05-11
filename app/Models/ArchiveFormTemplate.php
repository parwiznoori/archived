<?php

namespace App\Models;


use App\Traits\UseByGrade;
use App\Traits\UseBySemesterType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class ArchiveFormTemplate extends Model
{



    use UseByGrade, LogsActivity;

    protected $table = 'archive_form_templates';
    protected $fillable = ['form_name','status','variable','content'];

    protected $guarded = [];
    protected static $logUnguarded = true;
    protected $dates = ['deleted_at'];


//    // If 'variable' is stored as JSON, use casts
//    protected $casts = [
//        'variable' => 'array',
//    ];







}