<?php

namespace App\Models\Setting;

use App\Traits\Blameable;
use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $dataset_id
 * @property string $title
 * @property string $code
 * @property string $example
 * @property string $description
 * @property string $created_by
 * @property string $updated_by
 */

class AppDatasetDetail extends Model
{
    use Blameable, UUIDAsPrimaryKey;

    protected $guarded = [
        'id'
    ];

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(AppDataset::class);
    }

}
