<?php

namespace App\Models\PSA;

use App\Models\Setting\AppMasterData;
use App\Traits\Blameable;
use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string id
 * @property string agent_id
 * @property string customer_id
 * @property string title
 * @property string number
 * @property string date
 * @property string time
 * @property string product_id
 * @property string type_id
 * @property string note
 * @property string filename
 * @property string created_by
 * @property string updated_by
 */

class Plan extends Model
{
    use Blameable, UUIDAsPrimaryKey;

    const FILE_PATH = 'uploads/plans/';

    protected $guarded = [
        'id'
    ];

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
        return $this->belongsTo(AppMasterData::class, 'type_id');
    }

    public function realization(): HasOne
    {
        return $this->hasOne(Realization::class);
    }

}
