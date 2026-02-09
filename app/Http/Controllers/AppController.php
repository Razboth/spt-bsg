<?php

namespace App\Http\Controllers;

use App\Http\Requests\App\PasswordRequest;
use App\Models\Setting\AppMenu;
use App\Models\Setting\AppModul;
use App\Models\Setting\LocationLog;
use App\Models\Setting\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Str;

class AppController extends Controller
{
    public string $photoPath;

    public function __construct()
    {
        $this->middleware('auth');
        $this->photoPath = '/uploads/user/';
    }

    public function editProfile(String $route)
    {
        $user = User::find(Auth::id());
        $role = $user->roles()->first();

        return view('app.edit-profile', [
            'user' => $user,
            'role' => $role,
            'route' => $route
        ]);
    }

    public function updateProfile(Request $request, String $route)
    {
        $user = User::find(Auth::id());
        $photo = $user->photo;
        if($request->hasFile('photo')) $photo = uploadFile($request->file('photo'), Str::slug($request->input('name')).'_'.time(), $this->photoPath);

        $user->update([
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'description' => $request->input('description'),
            'photo' => $photo,
        ]);

        return response()->json([
            'success'=>'Profile berhasil disimpan',
            'url'=> route($route)
        ]);
    }

    public function editPassword(String $route)
    {
        return view('app.edit-password', [
            'route' => $route
        ]);
    }

    public function updatePassword(PasswordRequest $request, String $route)
    {
        $request->validate([
            'password' => 'required|confirmed',
        ]);

        $user = User::find(Auth::id());
        $user->update([
            'password' => Hash::make($request->input('password'))
        ]);

        return response()->json([
            'success'=>'Password berhasil disimpan',
            'url'=> route($route)
        ]);
    }

    public function currentLocation(Request $request)
    {
        LocationLog::create([
            'user_id' => Auth::user()->id,
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'address' => $request->input('address'),
        ]);
    }

    public function updateMenu()
    {
        $menus = AppMenu::with('modul')->get();

        foreach ($menus as $menu){
            $target = $menu->modul->target."/".$menu->target;
            $permissions = DB::table('app_permissions')->where('name', 'like', '%'.$target.'%')->where('type', 'menu')->get();
            foreach ($permissions as $permission){
                list($method, $menuTarget) = explode(' ', $permission->name);
                DB::table('app_permissions')->where('id', $permission->id)->update([
                    'type_id' => $menu->id,
                    'method' => $method,
                ]);
            }
        }

        $moduls = AppModul::get();

        foreach ($moduls as $modul){
            $target = $modul->target;
            $permissions = DB::table('app_permissions')->where('name', 'like', '%'.$target.'%')->where('type', 'modul')->get();
            foreach ($permissions as $permission){
                DB::table('app_permissions')->where('id', $permission->id)->update([
                    'type_id' => $modul->id
                ]);
            }
        }
    }
}
