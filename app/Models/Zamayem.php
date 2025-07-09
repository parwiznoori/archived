<?php

namespace App\Models;

use App\Traits\UseByGrade;
use App\Traits\UseBySemesterType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Morilog\Jalali\Jalalian;
use Spatie\Activitylog\Traits\LogsActivity;

class Zamayem extends Model
{
    use UseByGrade, LogsActivity;

    protected $table = 'archive_zamayems';
    protected $fillable = [
            'archivedata_id',
            'title',
            'zamayem_img',
          
        ]; 
    protected $guarded = [];
    protected static $logUnguarded = true;
    protected $dates = ['deleted_at'];


  public function archivedata()
    {
        return $this->belongsTo(Archivedata::class, 'archivedata_id');
    }

    


}