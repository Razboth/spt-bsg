<?php

namespace App\Models\PSA;

use App\Traits\Blameable;
use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string $id
 * @property string $agent_id
 * @property string $customer_id
 * @property string $product_id
 * @property string $month
 * @property string $year
 * @property int $value
 * @property string $approved_by
 * @property string $approved_status
 * @property string $approved_date
 * @property string $approved_note
 * @property string $created_by
 * @property string $updated_by
 */

class AgentBooster extends Model
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

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('agent_booster')
            ->setDescriptionForEvent(fn(string $eventName) => "Agent Booster has been {$eventName}")
            ->logUnguarded()
            ->logOnlyDirty();
    }
}
