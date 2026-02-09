<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\PSA\ProductService;
use App\Services\Setting\AppMasterDataService;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    private AppMasterDataService $appMasterDataService;

    public function __construct()
    {
        $this->appMasterDataService = new AppMasterDataService();
    }

    public function getMasters(string $code)
    {
        try {
            if(\Auth::check()){
                $masters = $this->appMasterDataService->getMasters($code)->get();
                return response()->json([
                    'message' => 'Success',
                    'data' => $masters
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
