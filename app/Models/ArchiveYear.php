<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
class ArchiveYear extends Model
{
    use LogsActivity;
    protected $table = 'archiveyears';
    protected $fillable = ['year'];


    public function archives()
{
    return $this->belongsToMany(Archive::class, 'archive_archive_year');
}

}
