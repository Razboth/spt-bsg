<?php

namespace App\Models\PSA;

use App\Models\Setting\AppMasterData;
use App\Traits\Blameable;
use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $plan_id
 * @property string $agent_id
 * @property string $customer_id
 * @property string $title
 * @property string $date
 * @property string $time
 * @property string $product_id
 * @property string $type_id
 * @property string $note
 * @property string $filename
 * @property int $closing_amount
 * @property string $account_number
 * @property string $approved_by
 * @property string $approved_status
 * @property string $approved_date
 * @property string $approved_note
 * @property float $latitude
 * @property float $longitude
 * @property string $address
 * @property string $created_by
 * @property string $updated_by
 */

class Realization extends Model
{
    use Blameable, UUIDAsPrimaryKey;

    protected $guarded = [
        'id',
    ];

    const FILE_PATH = 'uploads/realizations/';

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
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

    public function type(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class);
    }
}
