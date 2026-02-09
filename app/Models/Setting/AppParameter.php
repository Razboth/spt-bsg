<?php

namespace App\Models\Setting;

use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $code
 * @property string $name
 * @property string $value
 * @property string $description
 */

class AppParameter extends Model
{
    use UUIDAsPrimaryKey;
    protected $fillable = [
        'code',
        'name',
        'value',
        'description',
    ];
}
