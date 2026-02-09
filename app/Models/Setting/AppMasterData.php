<?php

namespace App\Models\Setting;

use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $parent_id
 * @property string $app_master_category_code
 * @property string $name
 * @property string $code
 * @property string $description
 * @property string $order
 * @property string $status
 */

class AppMasterData extends Model
{
    use UUIDAsPrimaryKey;

    protected $fillable = [
        'parent_id',
        'app_master_category_code',
        'name',
        'parameter',
        'secret_id',
        'code',
        'description',
        'order',
        'status',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(AppMasterData::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(AppMasterData::class, 'parent_id');
    }
}
