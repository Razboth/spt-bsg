<?php

namespace App\Providers;

use App\Models\Attendance\AttendanceApplication;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (config('app.force_https', true)) {
            URL::forceScheme('https');
        }

        if (php_sapi_name() != 'cli') {

            //GET DATA MODULS
            if (Cache::has('app_moduls')) {
                $data['moduls'] = Cache::get('app_moduls');
            } else {
                $data['moduls'] = DB::table('app_moduls')->where('status', 't')->orderBy('order')->get();
                Cache::forever('app_moduls', $data['moduls']);
            }

            //GET DATA MENUS
            if (Cache::has('app_menus')) {
                $data['menus'] = Cache::get('app_menus');
            } else {
                $data['menus'] = DB::table('app_menus')->where('status', 't')->get();
                Cache::forever('app_menus', $data['menus']);
            }

            //GET DATA SUB MODULS
            if (Cache::has('app_sub_moduls')) {
                $subModuls = Cache::get('app_sub_moduls');
            } else {
                $subModuls = DB::table('app_sub_moduls')->where('status', 't')->orderBy('order')->get();
                Cache::forever('app_sub_moduls', $subModuls);
            }

            $listFolder = [];
            foreach ($data['moduls'] as $key => $modul) {
                //GET LIST FOLDER FOR VIEW COMPOSER
                $listFolder[] = $modul->target . '.*';
            }

            if (Cache::has('app_info')) {
                $data['app_info'] = Cache::get('app_info');
            } else {
                $data['app_info'] = DB::table('app_infos')->first();
                Cache::forever('app_info', $data['app_info']);
            }

            $listFolder = array_merge($listFolder, ['layouts.app']);

            view()->composer($listFolder, function ($view) use ($data, $subModuls) {
                if (Auth::check()) {
                    $arrURL = explode('/', $this->app->request->getRequestUri());//GET URL
                    $data['module_path'] = $arrURL[1];
                    $data['param'] = !empty($arrURL[2]) ? explode('?', $arrURL[2])[1] ?? '' : ''; //GET LIST PARAM
                    $data['menu_path'] = !empty($arrURL[2]) ? explode('?', $arrURL[2])[0] ?? '' : ''; //GET LIST PARAM
                    $data['passed_id'] = !empty($arrURL[3]) ? $arrURL[3] : '';

                    $data['selected_modul'] = $data['moduls']->where('target', $data['module_path'])->first();
                    $data['selected_menu'] = $data['menus']->where('target', $data['menu_path'])->where('app_modul_id', $data['selected_modul']->id)->first();
                    $data['sub_moduls'] = $subModuls->where('app_modul_id', $data['selected_modul']->id);
                    $data['selected_sub_modul'] = $data['sub_moduls']->where('id', $data['selected_menu']->app_sub_modul_id)->first();
                    foreach ($data['sub_moduls'] as $key => $sub_modul) {
                        //SET FIRST MENU FROM ALL SUB MODUL FOR SUB MODUL BUTTON IN HEADER
                        $data['sub_moduls'][$key]->menus = $data['menus']->where('app_sub_modul_id', $sub_modul->id);
                    }

                    $view->with('app_moduls', $data['moduls']);
                    $view->with('selected_modul', $data['selected_modul']);
                    $view->with('selected_sub_modul', $data['selected_sub_modul']);
                    $view->with('selected_menu', $data['selected_menu']);
                    $view->with('app_sub_moduls', $data['sub_moduls']);
                    $view->with('param', $data['param']);
                    $view->with('menu_path', $data['module_path'] . "/" . $data['menu_path']);
                    $view->with('menu_target', $data['menu_path']);
                    $view->with('passed_id', $data['passed_id']);
                    $view->with('app_info', $data['app_info']);
                }
            });
        }
    }
}
