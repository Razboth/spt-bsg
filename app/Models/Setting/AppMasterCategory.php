<?php

namespace App\Models\Setting;

use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $parent_id
 * @property string $name
 * @property string $code
 * @property string $description
 * @property int $order
 */

class AppMasterCategory extends Model
{
    use UUIDAsPrimaryKey;

    protected $fillable = [
        'parent_id',
        'name',
        'code',
        'description',
        'order',
    ];
}
