<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
class Archiveqcstatus extends Model
{
    use LogsActivity;
    protected $table = 'archiveqcstatus';
    protected $fillable = ['qc_status'];
}
