<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\CustomerRequest;
use App\Models\PSA\Customer;
use App\Repositories\PSA\CustomerRepository;
use App\Services\PSA\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private CustomerService $customerService;
    private CustomerRepository $customerRepository;

    public function __construct()
    {
        $this->customerService = new CustomerService();
        $this->customerRepository = new CustomerRepository(
            new Customer()
        );
    }

    public function getCustomers(string $id)
    {
        try {
            if(\Auth::check()){
                $customers = $this->customerService->getCustomers()
                    ->where('agent_id', $id)
                    ->with(['category'])
                    ->get();
                return response()->json([
                    'message' => 'Success',
                    'data' => $customers
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

    public function store(Request $request)
    {
        try {
            if(\Auth::check()){
                $code = $this->customerService->getLastNumber();
                $data = [
                    'agent_id' => \Auth::user()->model_id,
                    'category_id' => $request->category_id,
                    'name' => $request->name,
                    'code' => $code,
                    'phone_number' => $request->phone_number,
                    'email' => $request->email,
                    'status' => 't'
                ];

                $this->customerRepository->create($data);
                return response()->json([
                    'message' => 'Success',
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

    public function update(Request $request, string $id)
    {
        try {
            if(\Auth::check()){
                $data = [
                    'category_id' => $request->category_id,
                    'name' => $request->name,
                    'phone_number' => $request->phone_number,
                    'email' => $request->email,
                ];

                $this->customerRepository->update($data, $id);
                return response()->json([
                    'message' => 'Success',
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

    public function delete(string $id)
    {
        try {
            if(\Auth::check()){
                $this->customerRepository->destroy($id);
                return response()->json([
                    'message' => 'Success',
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
