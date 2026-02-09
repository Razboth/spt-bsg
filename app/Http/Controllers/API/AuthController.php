<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PSA\ActivityDetail;
use App\Models\Setting\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $credentials = request(['username', 'password']);
        if(!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $user->phone_number = $user->model->phone_number;
        $user->position = $user->model->position->name;
        $user->role_name = $user->role->name;
        $user->email = $user->model->email;
        $user->model->position = $user->model->position->name;
        $user->model->type = $user->model->type->name;

        return response()->json([
            'message' => 'Success',
            'access_token' => $tokenResult->plainTextToken,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();

        return response()->json([
            'message' => 'Success'
        ]);
    }

    public function user()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->phone_number = $user->model->phone_number;
            $user->position = $user->model->position->name;
            $user->role_name = $user->role->name;
            $user->email = $user->model->email;
            $user->model->position = $user->model->position->name;
            $user->model->type = $user->model->type->name;
            return response()->json([
                'message' => 'Success',
                'data' => $user
            ]);
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            if(Auth::check()){
                $user = Auth::user();

                $user->update([
                    'name' => $request->input('name'),
                ]);

                $user->model->update([
                    'name' => $request->input('name'),
                    'phone_number' => $request->input('phone_number'),
                ]);

                if($request->hasFile('photo')) {
                    $photo = uploadFile(
                        $request->file('photo'),
                        Str::slug($user->name).'_'.time(),
                        '/uploads/user/');
                    $user->update([
                        'photo' => $photo
                    ]);
                }

                return response()->json([
                    'message' => 'Success',
                    'data' => $user
                ]);
            } else {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
