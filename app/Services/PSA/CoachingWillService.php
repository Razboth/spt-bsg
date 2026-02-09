<?php
namespace App\Services\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seam\CoachingWillRequest;
use App\Models\PSA\CoachingWill;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Str;

class CoachingWillService extends Controller
{
    public function getCoachingWills($columns = []): Builder
    {
        $query = CoachingWill::query();

        if (count($columns) > 0) {
            $query->select($columns);
        }

        return $query;
    }

    public function getCoachingWillById(string $id): CoachingWill
    {
        return CoachingWill::find($id);
    }

    public function save(CoachingWillRequest $request, string $id = ''): void
    {
        $realization = 0;
        if(isset($request->plan_date) && isset($request->action_date)){
            $planDate = Carbon::createFromFormat('d/m/Y',$request->plan_date);
            $actionDate = Carbon::createFromFormat('d/m/Y',$request->action_date);
            $realization = $planDate->diffInDays($actionDate, false);
        }
        $fields = [
            'coaching_id' => $request->coaching_id,
            'will' => $request->will,
            'plan_date' => isset($request->plan_date) ? resetDate($request->plan_date) : date('Y-m-d'),
            'action_date' => isset($request->action_date) ? resetDate($request->action_date) : '',
            'realization' => $realization,
            'note' => $request->note,
            'validation_status' => $request->validation_status,
            'validation_note' => $request->validation_note,
            'validation_at' => $request->validation_at,
            'validation_by' => $request->validation_by,
        ];

        if ($id != '') {
            $will = CoachingWill::find($id);
            $will->update($fields);
        } else {
            $will = CoachingWill::create($fields);
        }

        defaultUploadFile($will, $request, CoachingWill::FILE_PATH, $will->coaching->goal.'_' . Str::slug($will->will) . '_' . time());
    }

    public function saveWillInitial(CoachingWillRequest $request, string $id = ''): string
    {
        $fields = [
            'coaching_id' => $request->coaching_id,
            'will' => $request->will,
            'plan_date' => isset($request->plan_date) ? resetDate($request->plan_date) : date('Y-m-d'),
        ];

        if ($id != '') {
            $will = CoachingWill::find($id);
            $will->update($fields);
        } else {
            $will = CoachingWill::create($fields);
        }

        return $will->id;
    }

    public function saveValidation(Request $request, string $id = ''): void
    {
        $fields = [
            'validation_at' => resetDateTime($request->get('validation_at')),
            'validation_by' => \Auth::user()->id,
            'validation_status' => $request->get('validation_status'),
            'validation_note' => $request->get('validation_note'),
        ];

        $will = CoachingWill::find($id);
        $will->update($fields);
    }

    public function delete(string $id): void
    {
        $will = CoachingWill::find($id);
        $will?->delete();
    }
}
