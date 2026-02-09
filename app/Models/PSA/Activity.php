<?php

namespace App\Models\PSA;

use App\Traits\Blameable;
use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string $id
 * @property string $date
 * @property string $type
 * @property string $number
 * @property string $agent_id
 * @property string $customer_id
 * @property string $product_id
 * @property string $sub_product_id
 * @property string $approved_by
 * @property string $approved_status
 * @property string $approved_date
 * @property string $approved_note
 * @property string $approved_2_by
 * @property string $approved_2_status
 * @property string $approved_2_date
 * @property string $approved_2_note
 * @property string $created_by
 * @property string $updated_by
 */

class Activity extends Model
{
    use Blameable, UUIDAsPrimaryKey, LogsActivity;

    const FILE_PATH = 'uploads/activity/';

    const ACTIVITY_TYPES  = [
        'd' => 'Sales',
//        's' => 'Service',
//        'r' => 'Referral',
//        'c' => 'Cross Selling',
//        'u' => 'Upselling',
    ];

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

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function subProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(ActivityDetail::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded()->logOnlyDirty();
    }
}
