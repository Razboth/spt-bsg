<?php

namespace App\Models\Setting;

use App\Traits\Blameable;
use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $code
 * @property string $title
 * @property string $description
 * @property string $created_by
 * @property string $updated_by
 */

class AppDataset extends Model
{
    use Blameable, UUIDAsPrimaryKey;

    protected $guarded = [
        'id'
    ];

    public function details(): HasMany
    {
        return $this->hasMany(AppDatasetDetail::class, 'dataset_id');
    }
}
