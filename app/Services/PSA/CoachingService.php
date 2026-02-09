<?php
namespace App\Services\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\CoachingRequest;
use App\Http\Requests\Seam\CoachingWillRequest;
use App\Models\PSA\Coaching;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CoachingService extends Controller
{
    private CoachingWillService $coachingWillService;
    public function __construct()
    {
        $this->coachingWillService = new CoachingWillService();
    }

    public function getCoachings($columns = []): Builder
    {
        $query = Coaching::query();

        if (count($columns) > 0) {
            $query->select($columns);
        }

        return $query;
    }

    public function getCoachingByParticipantId(string $agentId): Collection
    {
        return $this->getCoachings()->where('agent_id', $agentId)
            ->get();
    }

    public function getCoachingById(string $id): Coaching
    {
        return Coaching::find($id);
    }

    public function save(CoachingRequest $request, string $id = ''): void
    {
        $fields = [
            'agent_id' => $request->agent_id,
            'instructor_id' => $request->instructor_id,
            'date' => resetDate($request->date),
            'goal' => $request->goal,
            'reality' => $request->reality,
            'option' => $request->option,
        ];

        if ($id != '') {
            $coaching = Coaching::find($id);
            $coaching->update($fields);
        } else {
            $coaching = Coaching::create($fields);
        }

        $coachingWills = $coaching->wills();

        $idsToKeep = [];

        $wills = $request->get('wills');

        if (!empty($wills)) {
            foreach ($wills as $willData) {
                if(isset($willData['will'])) {
                    $id = $willData['id'] ?? '';
                    if($id != '') {
                        $idsToKeep[] = $id;
                    }
                    $fields = [
                        'coaching_id' => $coaching->id,
                        'will' => $willData['will'],
                        'plan_date' => $willData['plan'],
                    ];

                    $id = $this->coachingWillService->saveWillInitial(new CoachingWillRequest($fields), $id);
                    $idsToKeep[] = $id;
                }
            }

            \Debugbar::info($idsToKeep);

            // Hapus semua wills yang tidak ada di request
            $coachingWills->whereNotIn('id', $idsToKeep)->delete();
        } else {
            // Jika request tidak memiliki wills, hapus semua wills yang terkait
            $coaching->wills()->delete();
        }
    }

    public function delete(string $id): void
    {
        Coaching::find($id)->delete();
    }
}
