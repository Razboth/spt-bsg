<?php

namespace App\Models\PSA;

use App\Models\Setting\AppMasterData;
use App\Traits\Blameable;
use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string $id
 * @property string $type
 * @property string $activity_id
 * @property string $type_id
 * @property string $plan_date
 * @property string $plan_time
 * @property string $plan_note
 * @property string $plan_filename
 * @property string $plan_document
 * @property string $realization_date
 * @property string $realization_time
 * @property string $realization_note
 * @property string $realization_filename
 * @property string $realization_document
 * @property int $closing_amount
 * @property int $customer_balance
 * @property string $account_number
 * @property string $cif
 * @property string $approved_by
 * @property string $approved_status
 * @property string $approved_date
 * @property string $approved_note
 * @property string $approved_2_by
 * @property string $approved_2_status
 * @property string $approved_2_date
 * @property string $approved_2_note
 * @property string $approved_3_by
 * @property string $approved_3_status
 * @property string $approved_3_note
 * @property string $approved_3_date
 * @property float $latitude
 * @property float $longitude
 * @property string $address
 * @property string $created_by
 * @property string $updated_by
 */

class ActivityDetail extends Model
{
    use Blameable, UUIDAsPrimaryKey, LogsActivity;

    protected $guarded = [
        'id'
    ];

    const FILE_PATH = 'uploads/activity/';

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

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function typeActivity(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded()->logOnlyDirty();
    }
}
