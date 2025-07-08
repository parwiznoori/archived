<?php

namespace App\Models;

use App\Traits\UseByGrade;
use App\Traits\UseBySemesterType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Morilog\Jalali\Jalalian;
use Spatie\Activitylog\Traits\LogsActivity;

class Baqidari extends Model
{
    use UseByGrade, LogsActivity;

    protected $table = 'baqidaris';
    protected $fillable = [
            'archivedata_id',
            'semester',
            'subject',
            'title',
            'chance',
            'chance_number',
            'zarib_chance',
            'credit',
            'monoghraph', 
            'zarib_credite', 
            'total_credit',
            'total_numbers',
            'semester_percentage',
            'total_credit2',
            'total_numbers2',
            'semester_percentage2',
            'total_credit3',
            'total_numbers3',
            'eight_semester_percentage3',
            'total_credit4',
            'total_numbers4',
            'eight_semester_percentage4',
            'baqidari_img'
        ]; 
    protected $guarded = [];
    protected static $logUnguarded = true;
    protected $dates = ['deleted_at'];


  public function archivedata()
    {
        return $this->belongsTo(Archivedata::class, 'archivedata_id');
    }

    


}