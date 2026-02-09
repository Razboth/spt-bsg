<?php

namespace App\Models\Setting;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $app_modul_id
 * @property string $name
 * @property string $description
 * @property int $order
 * @property string $status
 */

class AppSubModul extends Model
{
    protected $fillable = [
        'app_modul_id',
        'name',
        'description',
        'order',
        'status',
    ];

    public function appModul(): BelongsTo
    {
        return $this->belongsTo(AppModul::class);
    }

    public function appMenu(): HasMany
    {
        return $this->hasMany(AppMenu::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 't');
    }
}
