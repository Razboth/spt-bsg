<?php
namespace App\Services\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppNumberFormatRequest;
use App\Repositories\Setting\AppNumberFormatRepository;
use App\Models\Setting\AppNumberFormat;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppNumberFormatService extends Controller
{
    private AppNumberFormatRepository $appNumberFormatRepository;
    public function __construct()
    {
        $this->appNumberFormatRepository = new AppNumberFormatRepository(
            new AppNumberFormat()
        );
    }

    public function getNumberFormats($fields = []): Builder
    {
        $query = $this->appNumberFormatRepository->query();
        if ($fields) {
            $query->select($fields);
        }
        return $query;
    }

    public function getNumberFormatById(string $id): AppNumberFormat
    {
        return $this->appNumberFormatRepository->getById($id);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getNumberFormats();
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) {
                    $query->where('title', 'like', '%' . $filter . '%')->orWhere('code', 'like', '%' . $filter . '%');
                }
            };

            return generateDatatable($query, $queryFilter, [], true);
        }
    }

    public function save(AppNumberFormatRequest $request, string $id = ''): void
    {
        $fields = [
            'title' => $request->title,
            'code' => $request->code,
            'delimiter' => $request->delimiter,
            'example' => $request->example,
            'description' => $request->description,
        ];

        for ($i = 1; $i <= 5; $i++) {
            $fields['var' . $i . '_type'] = $request->get('var' . $i . '_type');
            if($request->get('var' . $i . '_type') == 'month') {
                $fields['var' . $i . '_value'] = $request->get('var' . $i . '_value_month');
            } else if($request->get('var' . $i . '_type') == 'year') {
                $fields['var' . $i . '_value'] = $request->get('var' . $i . '_value_year');
            } else {
                $fields['var' . $i . '_value'] = $request->get('var' . $i . '_value_text');
            }
        }

        if ($id != '') {
            $this->appNumberFormatRepository->update($fields, $id);
        } else {
            $this->appNumberFormatRepository->create($fields);
        }
    }

    public function delete(string $id): void
    {
        $this->appNumberFormatRepository->destroy($id);
    }
}
