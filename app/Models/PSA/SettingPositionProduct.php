<?php

namespace App\Models\PSA;

use App\Traits\Blameable;
use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string id
 * @property string name
 * @property string type
 * @property string positions
 * @property string description
 * @property string status
 * @property string created_by
 * @property string updated_by
 */

class SettingPositionProduct extends Model
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

    public function details(): HasMany
    {
        return $this->hasMany(SettingPositionProductDetail::class, 'setting_position_product_id', 'id');
    }
}
