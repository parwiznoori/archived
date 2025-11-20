<?php 

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

trait UseByUniversity
{
    protected static function bootUseByUniversity()
    {
        static::addGlobalScope('university', function ($query) {

            $user = auth()->user();

            if (!$user) {
                return;
            }

            $user_type = $user->user_type;

            // فقط تایپ 2 و 3 فیلتر شوند
            if ($user_type == 2 || $user_type == 3) {

                // اگر کاربر دانشگاه دارد
                if ($user->universities()->exists()) {
                    $query->whereIn(
                        $query->getQuery()->from . '.university_id',
                        $user->universities->pluck('id')
                    );
                }

                // اگر کاربر دانشگاه ندارد → هیچ فیلتری اعمال نشود (مهم!)
            }
        });

        static::saving(function (Model $model) {
            if (!isset($model->university_id) and auth()->user()->user_type == 3) {
                $model->university_id = auth()->user()->universities->first()->id ?? null;
            }
        });
    }

    public function scopeAllUniversities($query)
    {
        return $query->withoutGlobalScope('university');
    }

    public function university()
    {
        return $this->belongsTo(\App\Models\University::class);
    }

    public function universities()
    {
        return $this->belongsToMany(\App\Models\University::class,'university_users','user_id','university_id')->withTimestamps();
    }
}
