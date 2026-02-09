<?php

namespace App\Models\PSA;

use App\Traits\Blameable;
use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string id
 * @property string instructor_id
 * @property string agent_id
 * @property string goal
 * @property string reality
 * @property string option
 * @property string created_by
 * @property string updated_by
 */

class Coaching extends Model
{
    use Blameable, UUIDAsPrimaryKey;

    protected $guarded = ['id'];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'instructor_id');
    }

    public function wills(): HasMany
    {
        return $this->hasMany(CoachingWill::class);
    }

}
