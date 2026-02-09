<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property string $name
 * @property string $target
 * @property string $icon
 * @property string $description
 * @property integer $order
 * @property string $status
 */

class AppModul extends Model
{
    protected $fillable = [
        'name',
        'target',
        'icon',
        'description',
        'order',
        'status',
    ];

    public function subModul(): HasMany
    {
        return $this->hasMany(AppSubModul::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(AppMenu::class);
    }

    public function scopeActive($query, $id = "")
    {
        $query->where('status', 't');
        if($id)  $query->orWhere('id', $id);

        return $query;
    }
}
