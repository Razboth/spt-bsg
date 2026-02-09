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
 * @property string $type
 * @property string $period_id
 * @property string $unit_id
 * @property string $product_id
 * @property int $total
 * @property string $created_by
 * @property string $updated_by
 */

class TargetUnit extends Model
{
    use Blameable, UUIDAsPrimaryKey, LogsActivity;

    protected $guarded = [
        'id'
    ];

    public function details(): HasMany
    {
        return $this->hasMany(TargetUnitDetail::class, 'target_unit_id', 'id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(TargetPeriod::class, 'period_id', 'id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class, 'unit_id', 'id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function typeActivity(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'product_id', 'id');
    }

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
