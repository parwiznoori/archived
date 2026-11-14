<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;
class Archivedatastatus extends Model
{
    use LogsActivity;
    protected $table = 'archivedatastatus';
    protected $fillable = ['status'];
}
