<?php

namespace App\Models\PSA;

use App\Models\Setting\AppMasterData;
use App\Traits\Blameable;
use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string $id
 * @property string $date
 * @property string $unit_id
 * @property string $agent_id
 * @property string $customer_id
 * @property string $product_id
 * @property string $activity_detail_id
 * @property string $nominal
 * @property string $core_nominal
 * @property string $description
 * @property string $approved_by
 * @property string $approved_status
 * @property string $approved_date
 * @property string $approved_note
 * @property string $created_by
 * @property string $updated_by
 */

class SalesRatio extends Model
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

    public function unit(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class);
    }
}
