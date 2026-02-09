<?php

use App\Http\Controllers\PSA\ActivityController;
use App\Http\Controllers\PSA\AgentBoosterController;
use App\Http\Controllers\PSA\AgentController;
use App\Http\Controllers\PSA\AgentMappingController;
use App\Http\Controllers\PSA\AgentSalesTargetController;
use App\Http\Controllers\PSA\ApprovalController;
use App\Http\Controllers\PSA\BoomSettingController;
use App\Http\Controllers\PSA\ChangeCustomerController;
use App\Http\Controllers\PSA\CoachingController;
use App\Http\Controllers\PSA\CustomerController;
use App\Http\Controllers\PSA\DashboardController;
use App\Http\Controllers\PSA\NplSettingController;
use App\Http\Controllers\PSA\PerformanceController;
use App\Http\Controllers\PSA\PipelineController;
use App\Http\Controllers\PSA\ProductController;
use App\Http\Controllers\PSA\ProductMonthlySettingController;
use App\Http\Controllers\PSA\ReportController;
use App\Http\Controllers\PSA\SalesAverageRatioController;
use App\Http\Controllers\PSA\SalesDailyAverageController;
use App\Http\Controllers\PSA\SalesMonthlyRationController;
use App\Http\Controllers\PSA\SalesRatioController;
use App\Http\Controllers\PSA\SalesTargetController;
use App\Http\Controllers\PSA\ScheduleController;
use App\Http\Controllers\PSA\SettingPositionProductController;
use App\Http\Controllers\PSA\SptScoreController;
use App\Http\Controllers\PSA\TargetPeriodController;
use App\Http\Controllers\PSA\TypeController;
use Illuminate\Support\Facades\Route;


Route::name('psa.')->group(function () {
    Route::get('/psa/products/data', [ProductController::class, 'data'])->name('products.data');
    Route::get('/psa/products/{id}/data-child', [ProductController::class, 'dataChild'])->name('products.data-child');
    Route::get('/psa/products/{id}/create-child', [ProductController::class, 'createChild'])->name('products.create-child');
    Route::post('/psa/products/{id}/store-child', [ProductController::class, 'storeChild'])->name('products.store-child');
    Route::get('/psa/products/{id}/edit-child', [ProductController::class, 'editChild'])->name('products.edit-child');
    Route::patch('/psa/products/{id}/update-child', [ProductController::class, 'updateChild'])->name('products.update-child');
    Route::delete('/psa/products/{id}/destroy-child', [ProductController::class, 'destroyChild'])->name('products.destroy-child');
    Route::get('/psa/products/{id}/detail-product', [ProductController::class, 'detailProduct'])->name('products.detail-product');
    Route::resource('/psa/products', ProductController::class);

    Route::get('/psa/types/data', [TypeController::class, 'data'])->name('types.data');
    Route::resource('/psa/types', TypeController::class);

    Route::get('/psa/agents/data', [AgentController::class, 'data'])->name('agents.data');
    Route::get('/psa/agents/import', [AgentController::class, 'import'])->name('agents.import');
    Route::patch('/psa/agents/process-import', [AgentController::class, 'processImport'])->name('agents.process-import');
    Route::resource('/psa/agents', AgentController::class);

    Route::get('/psa/customers/data', [CustomerController::class, 'data'])->name('customers.data');
    Route::get('/psa/customers/import', [CustomerController::class, 'import'])->name('customers.import');
    Route::patch('/psa/customers/process-import', [CustomerController::class, 'processImport'])->name('customers.process-import');
    Route::resource('/psa/customers', CustomerController::class);

    Route::get('/psa/activities/data', [ActivityController::class, 'data'])->name('activities.data');
    Route::get('/psa/activities/create-detail/{id}', [ActivityController::class, 'createDetail'])->name('activities.create-detail');
    Route::post('/psa/activities/store-detail/{id}', [ActivityController::class, 'storeDetail'])->name('activities.store-detail');
    Route::get('/psa/activities/edit-detail/{id}', [ActivityController::class, 'editDetail'])->name('activities.edit-detail');
    Route::patch('/psa/activities/update-detail/{id}', [ActivityController::class, 'updateDetail'])->name('activities.update-detail');
    Route::delete('/psa/activities/destroy-detail/{id}', [ActivityController::class, 'destroyDetail'])->name('activities.destroy-detail');
    Route::get('/psa/activities/show-detail/{id}', [ActivityController::class, 'showDetail'])->name('activities.show-detail');
    Route::get('/psa/activities/products/{id}', [ActivityController::class, 'products'])->name('activities.products');
    Route::get('/psa/activities/sub-products/{id}', [ActivityController::class, 'subProducts'])->name('activities.sub-products');
    Route::get('/psa/activities/products-by-setting/{id}', [ActivityController::class, 'productBySetting'])->name('activities.products-by-setting');
    Route::get('/psa/activities/sales-by-setting/{id}', [ActivityController::class, 'salesBySetting'])->name('activities.sales-by-setting');
    Route::get('/psa/activities/edit-batch/{id}', [ActivityController::class, 'editBatch'])->name('activities.edit-batch');
    Route::patch('/psa/activities/save-batch/{id}', [ActivityController::class, 'updateBatch'])->name('activities.save-batch');
    Route::resource('/psa/activities', ActivityController::class);

    Route::get('/psa/agent-mappings', [AgentMappingController::class, 'index'])->name('agent-mappings.index');
    Route::get('/psa/agent-mappings/data', [AgentMappingController::class, 'data'])->name('agent-mappings.data');
    Route::get('/psa/agent-mappings/{id}/edit', [AgentMappingController::class, 'edit'])->name('agent-mappings.edit');
    Route::patch('/psa/agent-mappings/{id}/update', [AgentMappingController::class, 'update'])->name('agent-mappings.update');
    Route::get('/psa/agent-mappings/{id}/add-customer', [AgentMappingController::class, 'addCustomer'])->name('agent-mappings.add-customer');
    Route::get('/psa/agent-mappings/{id}/data-customer', [AgentMappingController::class, 'dataCustomer'])->name('agent-mappings.data-customer');
    Route::post('/psa/agent-mappings/{id}/store-customer', [AgentMappingController::class, 'storeCustomer'])->name('agent-mappings.store-customer');
    Route::delete('/psa/agent-mappings/{id}/destroy-customer', [AgentMappingController::class, 'destroyCustomer'])->name('agent-mappings.destroy-customer');

    Route::get('/psa/sales-target-account/data', [SalesTargetController::class, 'data'])->name('sales-target-account.data');
    Route::get('/psa/sales-target-account/edit-batch/{branchId}/{periodId}/{productId}', [SalesTargetController::class, 'editBatch'])->name('sales-target-account.edit-batch');
    Route::resource('/psa/sales-target-account', SalesTargetController::class);

    Route::get('/psa/sales-target-activity/data', [SalesTargetController::class, 'data'])->name('sales-target-activity.data');
    Route::get('/psa/sales-target-activity/edit-batch/{branchId}/{periodId}/{productId}', [SalesTargetController::class, 'editBatch'])->name('sales-target-activity.edit-batch');
    Route::resource('/psa/sales-target-activity', SalesTargetController::class);

    Route::get('/psa/agent-sales-target-account/data', [AgentSalesTargetController::class, 'data'])->name('agent-sales-target-account.data');
    Route::get('/psa/agent-sales-target-account/edit-batch/{branchId}/{periodId}/{productId}', [AgentSalesTargetController::class, 'editBatch'])->name('agent-sales-target-account.edit-batch');
    Route::resource('/psa/agent-sales-target-account', AgentSalesTargetController::class);

    Route::get('/psa/agent-sales-target-activity/data', [AgentSalesTargetController::class, 'data'])->name('agent-sales-target-activity.data');
    Route::get('/psa/agent-sales-target-activity/edit-batch/{branchId}/{periodId}/{productId}', [AgentSalesTargetController::class, 'editBatch'])->name('agent-sales-target-activity.edit-batch');
    Route::resource('/psa/agent-sales-target-activity', AgentSalesTargetController::class);

    Route::get('/psa/target-period/data', [TargetPeriodController::class, 'data'])->name('target-period.data');
    Route::get('/psa/target-period/approve/{detail}', [TargetPeriodController::class, 'approve'])->name('target-period.approve');
    Route::patch('/psa/target-period/update-approve/{detail}', [TargetPeriodController::class, 'updateApprove'])->name('target-period.update-approve');
    Route::patch('/psa/target-period/update-approve-2/{detail}', [TargetPeriodController::class, 'updateApprove2'])->name('target-period.update-approve-2');
    Route::resource('/psa/target-period', TargetPeriodController::class);

    Route::get('/psa/schedules', [ScheduleController::class, 'index'])->name('schedules.index');

    Route::get('/psa/pipelines', [PipelineController::class, 'index'])->name('pipelines.index');
    Route::get('/psa/sales-account', [PipelineController::class, 'salesAccount'])->name('sales-account.index');
    Route::get('/psa/sales-activity', [PipelineController::class, 'salesActivity'])->name('sales-activity.index');

    Route::get('/psa/performance', [PerformanceController::class, 'index'])->name('performance.index');
    Route::get('/psa/performance/{periodId}/{productId}/target-detail', [PerformanceController::class, 'performanceTargetDetail'])->name('performance.target-detail');
    Route::get('/psa/performance/{periodId}/{agentId}/agent-detail', [PerformanceController::class, 'performanceAgentDetail'])->name('performance.agent-detail');
    Route::get('/psa/performance/{periodId}/{unitId}/unit-detail', [PerformanceController::class, 'performanceUnitDetail'])->name('performance.unit-detail');
    Route::get('/psa/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/psa/home', [DashboardController::class, 'home'])->name('home.index');

    Route::get('/psa/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::get('/psa/approvals/data', [ApprovalController::class, 'data'])->name('approvals.data');
    Route::get('/psa/approvals/approve/{detail}', [ApprovalController::class, 'approve'])->name('approvals.approve');

    Route::patch('/psa/approvals/update-approve/{detail}', [ApprovalController::class, 'updateApprove'])->name('approvals.update-approve');
    Route::patch('/psa/approvals/update-approve-2/{detail}', [ApprovalController::class, 'updateApprove2'])->name('approvals.update-approve-2');
    Route::patch('/psa/approvals/update-approve-3/{detail}', [ApprovalController::class, 'updateApprove3'])->name('approvals.update-approve-3');
    Route::post('/psa/approvals/approve-batch', [ApprovalController::class, 'approveBatch'])->name('approvals.approve-batch');

    Route::get('/psa/approvals/approve-detail/{detail}', [ApprovalController::class, 'approveDetail'])->name('approvals.approve-detail');

    Route::patch('/psa/approvals/update-detail-approve/{detail}', [ApprovalController::class, 'updateDetailApprove'])->name('approvals.update-detail-approve');
    Route::patch('/psa/approvals/update-detail-approve-2/{detail}', [ApprovalController::class, 'updateDetailApprove2'])->name('approvals.update-detail-approve-2');
    Route::patch('/psa/approvals/update-detail-approve-3/{detail}', [ApprovalController::class, 'updateDetailApprove3'])->name('approvals.update-detail-approve-3');
    Route::post('/psa/approvals/approve-detail-batch', [ApprovalController::class, 'approveDetailBatch'])->name('approvals.approve-detail-batch');

    Route::get('/psa/approvals/{id}/detail', [ApprovalController::class, 'detail'])->name('approvals.detail');
    Route::get('/psa/approvals/products-by-setting/{id}', [ActivityController::class, 'productBySetting'])->name('approvals.products-by-setting');
    Route::get('/psa/approvals/sales-by-setting/{id}', [ActivityController::class, 'salesBySetting'])->name('approvals.sales-by-setting');

    Route::get('/psa/report-incentive-recipient', [ReportController::class, 'incentiveRecipient'])->name('report-incentive-recipient.index');
    Route::post('/psa/report-incentive-recipient/data', [ReportController::class, 'dataIncentiveRecipient'])->name('report-incentive-recipient.data');
    Route::get('/psa/report-incentive-recipient/export', [ReportController::class, 'exportIncentiveRecipient'])->name('report-incentive-recipient.export');

    Route::get('/psa/report-individual-spt', [ReportController::class, 'individualSpt'])->name('report-individual-spt.index');
    Route::get('/psa/report-individual-spt/data', [ReportController::class, 'dataIndividualSpt'])->name('report-individual-spt.data');
    Route::get('/psa/report-individual-spt/export', [ReportController::class, 'exportIndividualSpt'])->name('report-individual-spt.export');
    Route::get('/psa/report-individual-spt/{agent}/export-single', [ReportController::class, 'exportSingleIndividualSpt'])->name('report-individual-spt.export-single');

    Route::get('/psa/report-location-logs', [ReportController::class, 'locationLogs'])->name('report-location-logs.index');
    Route::get('/psa/report-location-logs/data', [ReportController::class, 'dataLocationLogs'])->name('report-location-logs.data');
    Route::get('/psa/report-location-logs/export', [ReportController::class, 'exportLocationLogs'])->name('report-location-logs.export');

    Route::get('/psa/maps-activity-sales', [ReportController::class, 'maps'])->name('maps-activity-sales.index');

    Route::get('/psa/setting-scores/data/{id}', [SptScoreController::class, 'data'])->name('setting-scores.data');
    Route::resource('/psa/setting-scores', SptScoreController::class);

    Route::get('/psa/setting-products/', [ProductMonthlySettingController::class, 'index'])->name('setting-products.index');
    Route::get('/psa/setting-products/data', [ProductMonthlySettingController::class, 'data'])->name('setting-products.data');
    Route::get('/psa/setting-products/{id}/{year}/edit', [ProductMonthlySettingController::class, 'edit'])->name('setting-products.edit');
    Route::patch('/psa/setting-products/{id}/{year}/update', [ProductMonthlySettingController::class, 'update'])->name('setting-products.update');

    Route::get('/psa/setting-position-products/data', [SettingPositionProductController::class, 'data'])->name('setting-position-products.data');
    Route::get('/psa/setting-position-products/{id}/create-product', [SettingPositionProductController::class, 'createProduct'])->name('setting-position-products.create-product');
    Route::post('/psa/setting-position-products/{id}/store-product', [SettingPositionProductController::class, 'storeProduct'])->name('setting-position-products.store-product');
    Route::get('/psa/setting-position-products/{id}/edit-product', [SettingPositionProductController::class, 'editProduct'])->name('setting-position-products.edit-product');
    Route::patch('/psa/setting-position-products/{id}/update-product', [SettingPositionProductController::class, 'updateProduct'])->name('setting-position-products.update-product');
    Route::delete('/psa/setting-position-products/{id}/destroy-product', [SettingPositionProductController::class, 'destroyProduct'])->name('setting-position-products.destroy-product');
    Route::resource('/psa/setting-position-products', SettingPositionProductController::class);

    Route::get('/psa/setting-position-boosters/data', [SettingPositionProductController::class, 'data'])->name('setting-position-boosters.data');
    Route::get('/psa/setting-position-boosters/{id}/create-product', [SettingPositionProductController::class, 'createProduct'])->name('setting-position-boosters.create-product');
    Route::post('/psa/setting-position-boosters/{id}/store-product', [SettingPositionProductController::class, 'storeProduct'])->name('setting-position-boosters.store-product');
    Route::get('/psa/setting-position-boosters/{id}/edit-product', [SettingPositionProductController::class, 'editProduct'])->name('setting-position-boosters.edit-product');
    Route::patch('/psa/setting-position-boosters/{id}/update-product', [SettingPositionProductController::class, 'updateProduct'])->name('setting-position-boosters.update-product');
    Route::delete('/psa/setting-position-boosters/{id}/destroy-product', [SettingPositionProductController::class, 'destroyProduct'])->name('setting-position-boosters.destroy-product');
    Route::resource('/psa/setting-position-boosters', SettingPositionProductController::class);

    Route::get('/psa/agent-boosters/data', [AgentBoosterController::class, 'data'])->name('agent-boosters.data');
    Route::get('/psa/agent-boosters/import', [AgentBoosterController::class, 'import'])->name('agent-boosters.import');
    Route::patch('/psa/agent-boosters/process-import', [AgentBoosterController::class, 'processImport'])->name('agent-boosters.process-import');
    Route::get('/psa/agent-boosters/approve/{booster}', [AgentBoosterController::class, 'approve'])->name('agent-boosters.approve');
    Route::patch('/psa/agent-boosters/update-approve/{id}', [AgentBoosterController::class, 'updateApprove'])->name('agent-boosters.update-approve');
    Route::resource('/psa/agent-boosters', AgentBoosterController::class);

    Route::get('/psa/change-customers/data', [ChangeCustomerController::class, 'data'])->name('change-customers.data');
    Route::get('/psa/change-customers/sales/{id}', [ChangeCustomerController::class, 'sales'])->name('change-customers.sales');
    Route::get('/psa/change-customers/customers/{id}', [ChangeCustomerController::class, 'customers'])->name('change-customers.customers');
    Route::get('/psa/change-customers/approve/{id}', [ChangeCustomerController::class, 'approve'])->name('change-customers.approve');
    Route::patch('/psa/change-customers/update-approve/{id}', [ChangeCustomerController::class, 'updateApprove'])->name('change-customers.update-approve');
    Route::resource('/psa/change-customers', ChangeCustomerController::class);

    Route::get('/psa/setting-boom/', [BoomSettingController::class, 'index'])->name('setting-boom.index');
    Route::get('/psa/setting-boom/data', [BoomSettingController::class, 'data'])->name('setting-boom.data');
    Route::get('/psa/setting-boom/{id}/{year}/edit', [BoomSettingController::class, 'edit'])->name('setting-boom.edit');
    Route::patch('/psa/setting-boom/{id}/{year}/update', [BoomSettingController::class, 'update'])->name('setting-boom.update');

    Route::get('/psa/setting-npl/', [NplSettingController::class, 'index'])->name('setting-npl.index');
    Route::get('/psa/setting-npl/data', [NplSettingController::class, 'data'])->name('setting-npl.data');
    Route::get('/psa/setting-npl/{id}/{year}/edit', [NplSettingController::class, 'edit'])->name('setting-npl.edit');
    Route::patch('/psa/setting-npl/{id}/{year}/update', [NplSettingController::class, 'update'])->name('setting-npl.update');

    Route::get('/psa/sales-ratio/data/{settingId}', [SalesRatioController::class, 'data'])->name('sales-ratio.data');
    Route::get('/psa/sales-ratio/sales/{id}', [SalesRatioController::class, 'sales'])->name('sales-ratio.sales');
    Route::get('/psa/sales-ratio/customers/{id}', [SalesRatioController::class, 'customers'])->name('sales-ratio.customers');
    Route::get('/psa/sales-ratio/products/{id}', [SalesRatioController::class, 'products'])->name('sales-ratio.products');
    Route::get('/psa/sales-ratio/approve/{id}', [SalesRatioController::class, 'approve'])->name('sales-ratio.approve');
    Route::patch('/psa/sales-ratio/update-approve/{id}', [SalesRatioController::class, 'updateApprove'])->name('sales-ratio.update-approve');
    Route::get('/psa/sales-ratio', [SalesRatioController::class, 'index'])->name('sales-ratio.index');
    Route::get('/psa/sales-ratio/{agentId}/{customerId}/{date}/edit', [SalesRatioController::class, 'edit'])->name('sales-ratio.edit');
    Route::patch('/psa/sales-ratio/update-approve/{agentId}/{customerId}/{date}', [SalesRatioController::class, 'update'])->name('sales-ratio.update');
    Route::get('/psa/sales-ratio/import', [SalesRatioController::class, 'import'])->name('sales-ratio.import');
    Route::patch('/psa/sales-ratio/process-import', [SalesRatioController::class, 'processImport'])->name('sales-ratio.process-import');

    Route::get('/psa/sales-monthly-ratio', [SalesMonthlyRationController::class, 'index'])->name('sales-monthly-ratio.index');
    Route::get('/psa/sales-monthly-ratio/data/{settingId}', [SalesMonthlyRationController::class, 'data'])->name('sales-monthly-ratio.data');

    Route::get('/psa/sales-average-ratio', [SalesAverageRatioController::class, 'index'])->name('sales-average-ratio.index');
    Route::get('/psa/sales-average-ratio/data/{settingId}', [SalesAverageRatioController::class, 'data'])->name('sales-average-ratio.data');

    Route::get('/psa/sales-daily-average', [SalesDailyAverageController::class, 'index'])->name('sales-daily-average.index');
    Route::get('/psa/sales-daily-average/data/{settingId}', [SalesDailyAverageController::class, 'data'])->name('sales-daily-average.data');
});