<?php

namespace App\Models\PSA;

use App\Traits\Blameable;
use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string name
 * @property string point
 * @property string description
 * @property string activity_type
 * @property string icon
 * @property string color
 * @property int order
 * @property string status
 */

class Type extends Model
{
    use Blameable, UUIDAsPrimaryKey, LogsActivity;

    protected $guarded = [
        'id'
    ];

    const ACTIVITY_TYPES = [
        'f' => 'Funding',
        'l' => 'Lending',
        's' => 'Syariah',
    ];

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
}
