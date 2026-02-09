<?php

namespace App\Models\PSA;

use App\Traits\Blameable;
use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TargetAgentDetail extends Model
{
    use Blameable, UUIDAsPrimaryKey, LogsActivity;

    protected $table = 'target_agent_details';

    protected $guarded = [
        'id'
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

    public function targetAgent(): BelongsTo
    {
        return $this->belongsTo(TargetAgent::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded()->logOnlyDirty();
    }
}
