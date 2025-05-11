<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ArchiveRole extends Model
{
    use LogsActivity;

    protected $table = 'archive_entry_users';
    protected $fillable = ['role_id','user_id', 'archive_id', 'status_id', 'qc_status_id'];

    public function archive()
    {
        return $this->belongsTo(Archive::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function archivedatastatus()
    {
        return $this->belongsTo(Archivedatastatus::class, 'status_id');

    }

    public function archiveqcstatus()
    {
        return $this->belongsTo(Archiveqcstatus::class,'qc_status_id');
    }


    public function role()
    {
        return $this->belongsTo(Role::class);
    }

}
