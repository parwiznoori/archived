<?php

namespace App\Models;

use App\Traits\UseByUniversity;
use App\Traits\UseByFaculty;
use App\Traits\UseByDepartment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class UniversityUser extends Model
{
    use SoftDeletes, UseByUniversity,UseByFaculty, UseByDepartment, LogsActivity;

    protected $table = 'university_users';
    protected $fillable = ['university_id','user_id'];

    protected $guarded = [];
    protected static $logUnguarded = true;
    protected $dates = ['deleted_at'];



    public function university()
    {
        return $this->belongsTo(University::class);
    }
  
    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

}