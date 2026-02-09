<?php
namespace App\Services\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppMasterTemplateRequest;
use App\Repositories\Setting\AppMasterTemplateRepository;
use App\Models\Setting\AppMasterTemplate;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppMasterTemplateService extends Controller
{
    private AppMasterTemplateRepository $appMasterTemplateRepository;
    public function __construct()
    {
        $this->appMasterTemplateRepository = new AppMasterTemplateRepository(
            new AppMasterTemplate()
        );
    }

    public function getMasterTemplates($fields = []): Builder
    {
        $query = $this->appMasterTemplateRepository->query();
        if ($fields) {
            $query->select($fields);
        }
        return $query;
    }

    public function getMasterTemplateById(string $id): AppMasterTemplate
    {
        return $this->appMasterTemplateRepository->getById($id);
    }

    public function getMasterTemplateForArray(): array
    {
        return $this->getMasterTemplates(['name', 'id'])->pluck('name', 'id')->toArray();
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getMasterTemplates();
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) {
                    $query->where('name', 'like', '%' . $filter . '%')->orWhere('code', 'like', '%' . $filter . '%');
                }
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'content', 'type' => 'custom', 'value' => function ($row) {
                    return \Str::length($row->content) > 150 ? \Str::substr($row->content, 0, 150).' ...' : $row->content;
                },
                ],
            ]);
        }
    }

    public function save(AppMasterTemplateRequest $request, string $id = ''): void
    {
        $fields = [
            'type' => $request->type,
            'dataset_id' => $request->dataset_id,
            'page_id' => $request->page_id,
            'name' => $request->name,
            'code' => $request->code,
            'content' => $request->content,
            'margin_bottom' => $request->margin_bottom,
            'margin_top' => $request->margin_top,
            'margin_left' => $request->margin_left,
            'margin_right' => $request->margin_right,
        ];

        if ($id != '') {
            $this->appMasterTemplateRepository->update($fields, $id);
        } else {
            $this->appMasterTemplateRepository->create($fields);
        }
    }

    public function delete(string $id): void
    {
        $this->appMasterTemplateRepository->destroy($id);
    }
}
