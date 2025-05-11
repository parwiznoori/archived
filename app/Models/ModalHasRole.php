<?php

namespace App\Models;

use App\Traits\UseByUniversity;
use App\Traits\UseByFaculty;
use App\Traits\UseByDepartment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class ModalHasRole extends Model
{


    protected $table = 'model_has_roles';
    protected $fillable = ['role_id','modal_type','model_id'];

    protected $guarded = [];
    protected static $logUnguarded = true;



}