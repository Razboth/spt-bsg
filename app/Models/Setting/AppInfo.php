<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;

class AppInfo extends Model
{
    protected $fillable = [
        'title',
        'primary_color',
        'light_primary_color',
        'background_light_primary_color',
        'login_page_title',
        'login_page_subtitle',
        'login_page_description',
        'login_page_logo',
        'login_page_background_image',
        'login_page_image',
        'footer_text',
        'year',
        'app_version',
        'app_logo',
        'app_logo_small',
        'app_icon',
        'location_log_status'
    ];
}
