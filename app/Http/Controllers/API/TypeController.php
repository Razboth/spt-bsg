<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\PSA\TypeService;
use App\Services\Setting\AppMasterDataService;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    private TypeService $typeService;

    public function __construct()
    {
        $this->typeService = new TypeService();
    }

    public function get()
    {
        try {
            if(\Auth::check()){
                $types = $this->typeService->getTypes()
                    ->orderBy('order')
                    ->get();
                return response()->json([
                    'message' => 'Success',
                    'data' => $types
                ]);
            }else{
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed',
                'data' => $e->getMessage()
            ]);
        }
    }
}
