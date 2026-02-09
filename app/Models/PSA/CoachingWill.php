<?php

namespace App\Models\PSA;

use App\Models\Seam\Coaching;
use App\Traits\Blameable;
use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string $id
 * @property string $coaching_id
 * @property string $will
 * @property string $plan_date
 * @property string $action_date
 * @property int $realization
 * @property string $filename
 * @property string $note
 * @property string $validation_status
 * @property string $validation_note
 * @property string $validation_at
 * @property string $validation_by
 * @property string $created_by
 * @property string $updated_by
 */

class CoachingWill extends Model
{
    use Blameable, UUIDAsPrimaryKey;

    protected $guarded = [
        'id'
    ];

    const FILE_PATH = 'uploads/will/';

    public function coaching(): BelongsTo
    {
        return $this->belongsTo(Coaching::class);
    }
}

