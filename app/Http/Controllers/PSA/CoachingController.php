<?php

namespace App\Http\Controllers\PSA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PSA\CoachingRequest;
use App\Http\Requests\Seam\CoachingWillRequest;
use App\Services\PSA\AgentService;
use App\Services\PSA\CoachingService;
use App\Services\PSA\CoachingWillService;
use App\Services\Setting\AppMasterDataService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Str;

class CoachingController extends Controller
{
    private AgentService $agentService;
    private CoachingService $coachingService;
    private AppMasterDataService $appMasterDataService;
    private CoachingWillService $coachingWillService;
    private array $arrValidations;
    public function __construct()
    {
        $this->middleware('auth');
        $this->agentService = new AgentService();
        $this->coachingService = new CoachingService();
        $this->appMasterDataService = new AppMasterDataService();
        $this->coachingWillService = new CoachingWillService();
        $this->arrValidations = [
            't' => 'Sesuai',
            'f' => 'Tidak Sesuai',
        ];

        \View::share('arrValidations', $this->arrValidations);
    }

    public function index()
    {
        if(\Auth::user()->type == 'p') {
            $participant = \Auth::user()->model;
            return $this->show($participant->programs->first()->program->id, $participant->id);
        }else {
            $model = \Auth::user()->model;
//            $program = $model->programs->first()->program;
            $positions = $this->appMasterDataService->getMasterForArray('AMPS');
            return view(Str::replace('/', '.', $this->menu_path()) . '.index', [
                'positions' => $positions,
//                'program' => $program,
            ]);
        }
    }

    /**
     * @throws Exception
     */
    public function data(Request $request)
    {
        return $this->agentService->dataCoaching($request, false, false);
    }

    public function show(string $agentId)
    {
        $agent = $this->agentService->getAgentById($agentId);
        $coachings = $this->coachingService->getCoachingByParticipantId($agentId);
        return view(Str::replace('/', '.', $this->menu_path()) . '.detail',[
            'agent' => $agent,
            'coachings' => $coachings
        ]);
    }

    public function create(string $participantId)
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.form',[
            'participantId' => $participantId,
            'instructorId' => \Auth::user()->id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CoachingRequest $request
     * @return JsonResponse
     */
    public function store(CoachingRequest $request)
    {
        return submitDataHelper(function () use ($request) {
            $this->coachingService->save($request);
        }, true, 'show', [$request->program_id, $request->participant_id]);
    }

    public function edit(string $coachingId)
    {
        $coaching = $this->coachingService->getCoachingById($coachingId);
        return view(Str::replace('/', '.', $this->menu_path()) . '.form',[
            'coaching' => $coaching,
            'participantId' => $coaching->participant_id,
            'instructorId' => $coaching->instructor_id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CoachingRequest $request
     * @param string $coachingId
     * @return JsonResponse
     */
    public function update(CoachingRequest $request, string $coachingId)
    {
        return submitDataHelper(function () use ($request, $coachingId) {
            $this->coachingService->save($request, $coachingId);
        }, true, 'show', [$request->participant_id]);
    }

    public function destroy(string $coachingId)
    {
        return deleteDataHelper(function () use ($coachingId) {
            $this->coachingService->delete($coachingId);
        });
    }

    public function createAction(string $willId)
    {
        $will = $this->coachingWillService->getCoachingWillById($willId);
        return view(Str::replace('/', '.', $this->menu_path()) . '.form-action',[
            'will' => $will,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CoachingWillRequest $request
     * @param string $willId
     * @return JsonResponse
     */
    public function saveAction(CoachingWillRequest $request, string $willId)
    {
        $will = $this->coachingWillService->getCoachingWillById($willId);
        return submitDataHelper(function () use ($request, $willId) {
            $this->coachingWillService->save($request, $willId);
        }, true, 'show', [$will->coaching->program_id, $will->coaching->participant_id]);
    }

    public function validation(string $willId)
    {
        $will = $this->coachingWillService->getCoachingWillById($willId);
        return view(Str::replace('/', '.', $this->menu_path()) . '.form-validation',[
            'will' => $will,
        ]);
    }

    public function saveValidation(Request $request, string $willId)
    {
        $will = $this->coachingWillService->getCoachingWillById($willId);
        return submitDataHelper(function () use ($request, $willId) {
            $this->coachingWillService->saveValidation($request, $willId);
        }, true, 'show', [$will->coaching->program_id, $will->coaching->participant_id]);
    }
}
