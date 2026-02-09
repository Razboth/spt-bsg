<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PSA\Customer;
use App\Models\PSA\Product;
use App\Repositories\PSA\CustomerRepository;
use App\Services\PSA\CustomerService;
use App\Services\PSA\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private ProductService $productService;
    
    public function __construct()
    {
        $this->productService = new ProductService();
    }

    public function getProducts()
    {
        try {
            if(\Auth::check()){
                $products = $this->productService->getProduct()->orderBy('order')->get();
                return response()->json([
                    'message' => 'Success',
                    'data' => $products
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
