<?php

namespace App\Models\Setting;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property integer $parent_id
 * @property integer $app_modul_id
 * @property integer $app_sub_modul_id
 * @property string $name
 * @property string $target
 * @property string $description
 * @property string $icon
 * @property string $parameter
 * @property string $full_screen
 * @property string $order
 * @property string $status
 *
 */

class AppMenu extends Model
{
    protected $fillable = [
        'parent_id',
        'app_modul_id',
        'app_sub_modul_id',
        'name',
        'target',
        'description',
        'icon',
        'parameter',
        'full_screen',
        'order',
        'status',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(AppMenu::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(AppMenu::class, 'parent_id');
    }

    public function modul(): BelongsTo
    {
        return $this->belongsTo(AppModul::class, 'app_modul_id');
    }

    public function subModul(): BelongsTo
    {
        return $this->belongsTo(AppSubModul::class, 'app_sub_modul_id');
    }

    public function scopeActive($query, $id = "")
    {
        $query->where('status', 't');
        if($id)  $query->orWhere('id', $id);

        return $query;
    }
}
