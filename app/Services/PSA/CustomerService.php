<?php
namespace App\Services\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\CustomerRequest;
use App\Models\PSA\Customer;
use App\Repositories\PSA\CustomerRepository;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomerService extends Controller
{
    private CustomerRepository $customerRepository;
    public function __construct()
    {
        $this->customerRepository = new CustomerRepository(
            new Customer()
        );
    }

    public function getCustomers($columns = []): Builder
    {
        $query = $this->customerRepository->query();

        if (count($columns) > 0) {
            $query->select($columns);
        }

        return $query;
    }

    public function getCustomerById(string $id): Customer
    {
        return $this->customerRepository->getById($id);
    }

    public function getCustomerForArray(): array
    {
        return $this->getCustomers(['id', 'name'])->pluck('name', 'id')->toArray();
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $filterCategory = $request->get('combo_1');
            $query = $this->getCustomers()
                ->when(\Auth::user()->role_id == 9, function (Builder $query) {
                    $query->where('agent_id', \Auth::user()->model_id);
                })
                ->when(\Auth::user()->role->name == 'Manager', function (Builder $query) {
                    $query->whereHas('agent', function (Builder $query) {
                        $query->where('leader_id', \Auth::user()->model_id);
                    });
                })
                ->when($filterCategory, function (Builder $query) use ($filterCategory) {
                $query->where('category_id', $filterCategory);
            });
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) {
                    $query->where('name', 'like', '%' . $filter . '%')->orWhere('code', 'like', '%' . $filter . '%');
                }
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'image', 'type' => 'photo'],
                ['name' => 'status', 'type' => 'status'],
            ]);
        }
    }

    /**
     * @throws Exception
     */
    public function dataMapping(Request $request, $id): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getCustomers();
            $filter = $request->get('search')['value'];
            $filterCategory = $request->get('combo_1');
            $query->whereNull('agent_id');
            $queryFilter = function ($query) use ($filter, $filterCategory) {
                if (isset($filter)) {
                    $query->where('name', 'like', '%' . $filter . '%')->orWhere('code', 'like', '%' . $filter . '%');
                }
                if(isset($filterCategory)) {
                    $query->where('category_id', $filterCategory);
                }
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'category_id', 'type' => 'master_relationship', 'masters' => 'category'],
                ['name' => 'check', 'type' => 'custom', 'value' => function ($row) {
                    return view('components.datatables.checkbox', ['id' => $row->id]);
                }],
            ], editable: false, deleteable: false);
        }
    }

    public function save(CustomerRequest $request, string $id = ''): Customer
    {
        $fields = [
            'category_id' => $request->category_id,
            'code' => $request->code,
            'name' => $request->name,
            'pic' => $request->pic,
            'phone_number' => $request->phone_number,
            'identity_number' => $request->identity_number,
            'email' => $request->email,
            'address' => $request->address,
            'description' => $request->description,
            'status' => $request->status,
        ];

        if ($id != '') {
            $customer = $this->customerRepository->update($fields, $id);
        } else {
            $customer = $this->customerRepository->create($fields);
        }

        defaultUploadFile($customer, $request, Customer::FILE_PATH, 'customer_' . Str::slug($customer->name) . '_' . time(), "image");

        if(\Auth::user()->role_id == 9){
            $customer->agent_id = \Auth::user()->model_id;
            $customer->save();
        }

        return $customer;
    }

    public function delete(string $id): void
    {
        $this->customerRepository->destroy($id);
    }

    public function getLastNumber(): string
    {
        return generateFormatNumber('FPC', 'customers', 'code');
    }
}
