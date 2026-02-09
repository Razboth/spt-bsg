<?php

namespace App\Models\Setting;

use App\Traits\Blameable;
use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $type
 * @property string $dataset_id
 * @property string $page_id
 * @property string $name
 * @property string $code
 * @property string $content
 * @property string $margin_bottom
 * @property string $margin_top
 * @property string $margin_left
 * @property string $margin_right
 * @property string $created_by
 * @property string $updated_by
 */

class AppMasterTemplate extends Model
{
    use Blameable, UUIDAsPrimaryKey;

    protected $guarded = [
        'id'
    ];

    const TEMPLATE_OPTIONS = [
        '0' => 'Dokumen',
        '1' => 'Email',
    ];

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(AppDataset::class);
    }
}
