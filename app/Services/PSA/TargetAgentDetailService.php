<?php
namespace App\Services\PSA;

use App\Http\Controllers\Controller;
use App\Models\PSA\TargetAgentDetail;
use App\Repositories\PSA\TargetAgentDetailRepository;
use Illuminate\Database\Eloquent\Builder;

class TargetAgentDetailService extends Controller
{
    private TargetAgentDetailRepository $targetAgentDetailRepository;
    public function __construct()
    {
        $this->targetAgentDetailRepository = new TargetAgentDetailRepository(
            new TargetAgentDetail()
        );
    }

    public function getDetails($columns = []): Builder
    {
        $query = $this->targetAgentDetailRepository->query();

        if (count($columns) > 0) {
            $query->select($columns);
        }

        return $query;
    }

    public function getDetailById(string $id): TargetAgentDetail
    {
        return $this->targetAgentDetailRepository->getById($id);
    }

    public function getDetailForArray(): array
    {
        return $this->getDetails(['id', 'name'])->pluck('name', 'id')->toArray();
    }

    public function save(array $arr): void
    {
        $this->targetAgentDetailRepository->updateOrCreate([
            'amount' => resetCurrency($arr['amount']),
        ],[
            'target_agent_id' => $arr['target_agent_id'],
            'month' => $arr['month'],
            'year' => $arr['year'],
        ]);
    }
}
