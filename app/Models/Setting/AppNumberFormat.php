<?php

namespace App\Models\Setting;

use App\Traits\Blameable;
use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string id
 * @property string title
 * @property string code
 * @property string delimiter
 * @property string var1_type
 * @property string var1_value
 * @property string var2_type
 * @property string var2_value
 * @property string var3_type
 * @property string var3_value
 * @property string var4_type
 * @property string var4_value
 * @property string var5_type
 * @property string var5_value
 * @property string example
 * @property string description
 * @property string created_by
 * @property string updated_by
 */
class AppNumberFormat extends Model
{
    use Blameable, UUIDAsPrimaryKey;

    const variableOptions = [
        'sequence' => 'Sequence',
        'code' => 'Kode',
        'month' => 'Bulan',
        'year' => 'Tahun',
    ];

    const delimiterOptions = [
        '/' => '/ | Garis Miring',
        '-' => '- | Strip',
        '.' => '- | Titik',
        '_' => '_ | Underscore',
    ];

    const monthOptions = [
        'roman' => 'Romawi',
        'number' => 'Angka',
    ];

    const yearOptions = [
        '2digit' => '2 Digit',
        '4digit' => '4 Digit',
    ];

    protected $guarded = ['id'];
}
