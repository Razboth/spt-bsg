<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckEmployeeData
{
    /**
     * Blokir akses menu kalau user butuh data pegawai (agents) tapi belum dimapping,
     * supaya tidak error 500 di controller/service yang memakai model_id.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->needsEmployee() || $user->hasEmployee()) {
            return $next($request);
        }

        $message = 'Akun Anda belum terhubung dengan data pegawai. Hubungi admin untuk melakukan mapping data pegawai terlebih dahulu.';

        if ($request->ajax() || $request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => false,
                'message' => $message,
            ], 403);
        }

        return response()->view('errors.no-employee', [
            'message' => $message,
        ], 403);
    }
}
