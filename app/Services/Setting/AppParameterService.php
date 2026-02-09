<?php

namespace App\Services\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppParameterRequest;
use App\Models\Setting\AppParameter;
use App\Repositories\Setting\AppParameterRepository;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppParameterService extends Controller
{
    private AppParameterRepository $appParameterRepository;

    public function __construct()
    {
        $this->appParameterRepository = new AppParameterRepository(
            new AppParameter()
        );
    }

    public function getParameters(): Builder
    {
        return $this->appParameterRepository->query();
    }

    public function getParameterById(string $id): AppParameter
    {
        return $this->appParameterRepository->getById($id);
    }

    public function getParameterByCode(string $code): AppParameter
    {
        return $this->appParameterRepository->getByCode($code);
    }

    public function getParametersWithSpecificColumn(array $columns): Builder
    {
        return $this->getParameters()->select($columns);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getParameters();
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) $query->where('name', 'like', "%{$filter}%")->orWhere('code', 'like', "%{$filter}%");
            };

            return generateDatatable($query, $queryFilter,[],true);
        }
    }

    public function saveParameter(AppParameterRequest $request, string $id = ''): void
    {

        $fields = [
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'value' => $request->input('value'),
            'description' => $request->input('description'),
        ];

        if ($id == '')
            $this->appParameterRepository->create($fields);
        else
            $this->appParameterRepository->update($fields, $id);
    }

    public function deleteParameter(string $id): void
    {
        $this->appParameterRepository->destroy($id);
    }
}