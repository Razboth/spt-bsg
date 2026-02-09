<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function menu_path()
    {
        $arrURL = explode('/', \Request::url());//GET URL
        $modul = !empty($arrURL[3]) ? $arrURL[3] : ''; //GET MODUL
        $menu = !empty($arrURL[4]) ? explode('?', $arrURL[4])[0] ?? '' : ''; //GET MENU WITHOUT PARAM

        return $modul.'/'.$menu;
    }

    public function defaultPagination($request)
    {
        return $request->get('paginate') ?? 10;
    }
}
