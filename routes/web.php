<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\MigrationController;
use App\Http\Controllers\Setting\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        $firstMenu = DB::table('app_menus as t1')
            ->select('t1.id', 't4.name')
            ->join('app_moduls as t2', 't1.app_modul_id', 't2.id')
            ->join('app_sub_moduls as t3', 't1.app_sub_modul_id', 't3.id')
            ->join('app_permissions as t4', 't1.id', 't4.type_id')
            ->join('app_role_has_permissions as t5', 't4.id', 't5.permission_id')
            ->where('t1.status', 't')
            ->where('t2.status', 't')
            ->where('t4.method', 'view')
            ->whereNull('t1.parent_id')
            ->where('t4.type', 'menu')
            ->where('t5.role_id', Auth::user()->role_id)
            ->orderBy('t2.order')
            ->orderBy('t3.order')
            ->orderBy('t1.order')
            ->first();
        $checkChild = DB::table('app_menus as t1')
            ->select('t4.name')
            ->join('app_moduls as t2', 't1.app_modul_id', 't2.id')
            ->join('app_sub_moduls as t3', 't1.app_sub_modul_id', 't3.id')
            ->join('app_permissions as t4', 't1.id', 't4.type_id')
            ->join('app_role_has_permissions as t5', 't4.id', 't5.permission_id')
            ->where('t1.status', 't')
            ->where('t2.status', 't')
            ->where('t4.method', 'view')
            ->where('t4.type', 'menu')
            ->where('t5.role_id', Auth::user()->role_id)
            ->where('t1.parent_id', $firstMenu->id)
            ->orderBy('t2.order')
            ->orderBy('t3.order')
            ->orderBy('t1.order')
            ->first();
        if($checkChild){
            $route = str_replace("view ", "", str_replace("/", ".", $checkChild->name) . ".index");
        }else {
            $route = str_replace("view ", "", str_replace("/", ".", $firstMenu->name) . ".index");
        }

        return redirect()->route($route);
    } else {
        $app_info = DB::table('app_infos')->first();

        return view('auth.login', compact('app_info'));
    }
})->name('default');

//SET IF 404 THEN GO TO SOME ROUTE
Route::fallback(function () {
    return redirect()->route('default');
});


Auth::routes();

//PSA ROUTE
require_once __DIR__ . '/web/psa.php';

//SETTING ROUTE
require_once __DIR__ . '/web/setting.php';

//MIGRATE ROUTE
require_once __DIR__ . '/web/migrate.php';

//APP ROUTES
Route::get('/app/edit-profile/{currentRoute}', [AppController::class, 'editProfile'])->name('app.edit-profile');
Route::patch('/app/update-profile/{currentRoute}', [AppController::class, 'updateProfile'])->name('app.update-profile');
Route::get('/app/edit-password/{currentRoute}', [AppController::class, 'editPassword'])->name('app.edit-password');
Route::patch('/app/update-password/{currentRoute}', [AppController::class, 'updatePassword'])->name('app.update-password');

Route::get('/app/update-menu', [AppController::class, 'updateMenu'])->name('app.update-menu');

Route::get('/migration/update-pin', [MigrationController::class, 'updatePin'])->name('migration.update-pin');

Route::get('/migration/create-branch', [MigrationController::class, 'createBranch'])->name('migration.create-branch');

Route::get('/clear', function () {
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');
    Artisan::call('route:clear');
    Artisan::call('cache:clear');

    return "Cleared!";

});

Route::post('/app/current-location/', [AppController::class, 'currentLocation'])->name('app.current-location');

Route::get('/reload-captcha', function () {
    return response()->json(['captcha' => captcha_img()]);
});

Route::get('/artisan-migrate', function (){
    Artisan::call('migrate');
    return "Migrated!";
});
