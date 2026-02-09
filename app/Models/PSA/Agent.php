<?php

namespace App\Models\PSA;

use App\Models\Setting\AppMasterData;
use App\Traits\Blameable;
use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string $id
 * @property string $name
 * @property string $nickname
 * @property string $employee_number
 * @property string $identity_number
 * @property string $birth_place
 * @property string $birth_date
 * @property string $phone_number
 * @property string $email
 * @property string $address
 * @property string $gender
 * @property string $status_id
 * @property string $type_id
 * @property string $rank_id
 * @property string $position_id
 * @property string $join_date
 * @property string $leave_date
 * @property string $identity_file
 * @property string $photo
 * @property string $activity_type
 * @property string $leader_id
 * @property string $unit_id
 * @property string $created_by
 * @property string $updated_by
 */

class Agent extends Model
{
    use Blameable, UUIDAsPrimaryKey, LogsActivity;

    protected $guarded = [
        'id',
    ];

    const FILE_PATH = 'uploads/agents/';

    const ACTIVITY_TYPES = [
        'a' => 'Semua',
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

    public function status(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class);
    }

    public function rank(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class);
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded()->logOnlyDirty();
    }
}
