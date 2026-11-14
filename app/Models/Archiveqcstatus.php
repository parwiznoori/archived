<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;
class Archiveqcstatus extends Model
{
    use LogsActivity;
    protected $table = 'archiveqcstatus';
    protected $fillable = ['qc_status'];
}
