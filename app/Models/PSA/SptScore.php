<?php

namespace App\Models\PSA;

use App\Traits\Blameable;
use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string $id
 * @property string $setting_id
 * @property string $score
 * @property string $minimum_value
 * @property string $maximum_value
 * @property string $note
 * @property string $created_by
 * @property string $updated_by
 */

class SptScore extends Model
{
    use Blameable, UUIDAsPrimaryKey, LogsActivity;

    protected $guarded = ['id'];

    public function tapActivity(\Spatie\Activitylog\Models\Activity $activity, string $eventName)
    {
        $agent = new \Jenssegers\Agent\Agent();

        $activity->properties = $activity->properties->merge([
            'ip' => request()->ip(),
            'browser' => $agent->browser(),
            'device' => $agent->device(),
            'platform' => $agent->platform(),
            'user_agent' => $agent->getUserAgent(),
        ]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded()->logOnlyDirty();
    }

    public function setting()
    {
        return $this->belongsTo(SettingPositionProduct::class, 'setting_id');
    }
}
