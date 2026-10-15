<?php

use App\Http\Controllers\API\ActivityController;
use App\Http\Controllers\API\AgentController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\LeaderController;
use App\Http\Controllers\API\MasterController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\TypeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'employee.data'])->group(function (){
    Route::get('/activities', [ActivityController::class, 'get']);
    Route::get('/activity/{id}', [ActivityController::class, 'getActivity']);
    Route::post('/activity/store', [ActivityController::class, 'store']);
    Route::post('/activity/update/{id}', [ActivityController::class, 'update']);
    Route::get('/activityDetails/{id}', [ActivityController::class, 'getDetails']);
    Route::get('/activity-detail/{id}', [ActivityController::class, 'getDetail']);
    Route::get('/allActivityDetails', [ActivityController::class, 'getAllDetails']);

    Route::post('/activity/plan/store/{id}', [ActivityController::class, 'storePlan']);
    Route::post('/activity/plan/update/{id}', [ActivityController::class, 'updatePlan']);
    Route::post('/activity/realization/store/{id}', [ActivityController::class, 'storeRealization']);
    Route::post('/activity/realization/update/{id}', [ActivityController::class, 'updateRealization']);
    Route::post('/activity/realization/batch-approve', [ActivityController::class, 'submitBatchApproval']);
    Route::post('/activity/realization/approve/{id}', [ActivityController::class, 'submitApproval']);
    Route::delete('/activity/delete/{id}', [ActivityController::class, 'delete']);

    Route::get('/products', [ProductController::class, 'getProducts']);

    Route::get('/masters/{code}', [MasterController::class, 'getMasters']);

    Route::get('/types', [TypeController::class, 'get']);

    Route::get('/customers/{id}', [CustomerController::class, 'getCustomers']);
    Route::post('/customer/store', [CustomerController::class, 'store']);
    Route::post('/customer/update/{id}', [CustomerController::class, 'update']);
    Route::delete('/customer/delete/{id}', [CustomerController::class, 'delete']);

    Route::get('/leader/products', [LeaderController::class, 'getProducts']);
    Route::get('/leader/agents', [LeaderController::class, 'getAgents']);
    Route::get('/leader/agent/{id}', [LeaderController::class, 'getAgent']);
    Route::get('/leader/targets/{id}/{month}/{year}', [LeaderController::class, 'getTargets']);
    Route::get('/leader/target-agents/{id}/{month}/{year}', [LeaderController::class, 'getTargetAgents']);

    Route::get('/agent/target/{id}/{month}/{year}', [AgentController::class, 'getTarget']);

    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});
