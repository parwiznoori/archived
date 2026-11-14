<?php

namespace App\Traits;

use Spatie\Activitylog\LogOptions;

trait LogsActivity
{
    use \Spatie\Activitylog\Traits\LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        $options = LogOptions::defaults()->logExcept(['password', 'remember_token']);
        if (isset(static::$logName)) {
            $options->useLogName(static::$logName);
        }
        if (isset(static::$logAttributes)) {
            $options->logOnly(static::$logAttributes);
        }
        if (static::$logUnguarded ?? false) {
            $options->logUnguarded();
        }
        if (static::$logFillable ?? false) {
            $options->logFillable();
        }
        if (static::$logOnlyDirty ?? false) {
            $options->logOnlyDirty();
        }
        return $options;
    }
}
