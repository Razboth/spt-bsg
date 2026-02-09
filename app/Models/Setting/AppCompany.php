<?php

namespace App\Models\Setting;

/**
 * @property string name
 * @property string address
 * @property string phone_number
 * @property string fax
 * @property string email
 * @property string website
 * @property string logo
 * @property string description
 */

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppCompany extends Model
{
    use Blameable;

    const LOGO_PATH = '/uploads/setting/company/logo/';

    protected $fillable = [
        'name',
        'address',
        'phone_number',
        'fax',
        'email',
        'website',
        'logo',
        'description',
    ];
}
