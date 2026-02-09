<?php

namespace App\Models\PSA;

use App\Models\Setting\AppMasterData;
use App\Traits\Blameable;
use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string id
 * @property string agent_id
 * @property string category_id
 * @property string code
 * @property string name
 * @property string pic
 * @property string email
 * @property string phone_number
 * @property string address
 * @property string description
 * @property string image
 * @property string status
 * @property string created_by
 * @property string updated_by
 */
class Customer extends Model
{
    use Blameable, UUIDAsPrimaryKey, LogsActivity;

    protected $table = 'customers';

    protected $guarded = [
        'id'
    ];

    public function tapActivity(Activity $activity, string $eventName)
    {
        $agent = new Agent();

        $activity->properties = $activity->properties->merge([
            'ip' => request()->ip(),
            'browser' => $agent->browser(),
            'device' => $agent->device(),
            'platform' => $agent->platform(),
            'user_agent' => $agent->getUserAgent(),
        ]);
    }

    const FILE_PATH = 'uploads/customers/';

    public function category(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class, 'category_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded()->logOnlyDirty();
    }
}