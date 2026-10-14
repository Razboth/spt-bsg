<?php

use App\Http\Controllers\Setting\AppCompanyController;
use App\Http\Controllers\Setting\AppDatasetController;
use App\Http\Controllers\Setting\AppInfoController;
use App\Http\Controllers\Setting\AppMasterDataController;
use App\Http\Controllers\Setting\AppMasterTemplateController;
use App\Http\Controllers\Setting\AppMenuController;
use App\Http\Controllers\Setting\AppModulController;
use App\Http\Controllers\Setting\AppNumberFormatController;
use App\Http\Controllers\Setting\AppParameterController;
use App\Http\Controllers\Setting\AppSubModulController;
use App\Http\Controllers\Setting\ChecklistMenuController;
use App\Http\Controllers\Setting\LogActivityController;
use App\Http\Controllers\Setting\MenuChecklistController;
use App\Http\Controllers\Setting\UserAccessController;
use App\Http\Controllers\Setting\UserController;
use App\Http\Controllers\Setting\UserRoleController;
use Illuminate\Support\Facades\Route;

Route::middleware('menu.access')->name('settings.')->group(function () {
    Route::get('/settings/users/reset-password/{id}', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::patch('/settings/users/update-password/{id}', [UserController::class, 'updatePassword'])->name('users.update-password');
    Route::resource('/settings/users', UserController::class);

    Route::resource('/settings/user-roles', UserRoleController::class);

    Route::resource('/settings/user-access', UserAccessController::class);

    Route::get('/settings/app-moduls/data', [AppModulController::class, 'data'])->name('app-moduls.data');
    Route::resource('/settings/app-moduls', AppModulController::class);

    Route::get('/settings/app-sub-moduls/data', [AppSubModulController::class, 'data'])->name('app-sub-moduls.data');
    Route::resource('/settings/app-sub-moduls', AppSubModulController::class);

    Route::resource('/settings/app-menus', AppMenuController::class);
    Route::get('/settings/app-menus/sub-moduls/{id}', [AppMenuController::class, 'subModuls'])->name('app-menus.sub-moduls');
    Route::get('/settings/app-menus/menus/{id}', [AppMenuController::class, 'menus'])->name('app-menus.menus');
    Route::get('/settings/app-menus/{id}/create-child', [AppMenuController::class, 'createChild'])->name('app-menus.create-child');

    Route::get('/settings/app-master-datas/{id}/create-master', [AppMasterDataController::class, 'createMaster'])->name('app-master-datas.create-master');
    Route::post('/settings/app-master-datas/{id}/store-master', [AppMasterDataController::class, 'storeMaster'])->name('app-master-datas.store-master');
    Route::get('/settings/app-master-datas/{id}/edit-master', [AppMasterDataController::class, 'editMaster'])->name('app-master-datas.edit-master');
    Route::patch('/settings/app-master-datas/{id}/update-master', [AppMasterDataController::class, 'updateMaster'])->name('app-master-datas.update-master');
    Route::delete('/settings/app-master-datas/{id}/destroy-master', [AppMasterDataController::class, 'destroyMaster'])->name('app-master-datas.destroy-master');
    Route::resource('/settings/app-master-datas', AppMasterDataController::class);

    Route::get('/settings/app-parameters/data', [AppParameterController::class, 'data'])->name('app-parameters.data');
    Route::resource('/settings/app-parameters', AppParameterController::class);

    Route::get('/settings/app-info/data', [AppInfoController::class, 'data'])->name('app-info.data');
    Route::get('/settings/app-info', [AppInfoController::class, 'index'])->name('app-info.index');
    Route::patch('/settings/app-info', [AppInfoController::class, 'update'])->name('app-info.update');

    Route::get('/settings/app-company/data', [AppCompanyController::class, 'data'])->name('app-company.data');
    Route::get('/settings/app-company', [AppCompanyController::class, 'index'])->name('app-company.index');
    Route::patch('/settings/app-company', [AppCompanyController::class, 'update'])->name('app-company.update');

    Route::get('/settings/app-number-formats/data', [AppNumberFormatController::class, 'data'])->name('app-number-formats.data');
    Route::resource('/settings/app-number-formats', AppNumberFormatController::class);

    Route::get('/settings/app-datasets/data', [AppDatasetController::class, 'data'])->name('app-datasets.data');
    Route::get('/settings/app-datasets/{id}/create-detail', [AppDatasetController::class, 'createDetail'])->name('app-datasets.create-detail');
    Route::post('/settings/app-datasets/{id}/store-detail', [AppDatasetController::class, 'storeDetail'])->name('app-datasets.store-detail');
    Route::get('/settings/app-datasets/{id}/edit-detail', [AppDatasetController::class, 'editDetail'])->name('app-datasets.edit-detail');
    Route::patch('/settings/app-datasets/{id}/update-detail', [AppDatasetController::class, 'updateDetail'])->name('app-datasets.update-detail');
    Route::delete('/settings/app-datasets/{id}/destroy-detail', [AppDatasetController::class, 'destroyDetail'])->name('app-datasets.destroy-detail');
    Route::get('/settings/app-datasets/{id}/data-detail', [AppDatasetController::class, 'dataDetail'])->name('app-datasets.data-detail');
    Route::resource('/settings/app-datasets', AppDatasetController::class);

    Route::get('/settings/app-master-templates/data', [AppMasterTemplateController::class, 'data'])->name('app-master-templates.data');
    Route::get('/settings/app-master-templates/get-dataset/{id}', [AppMasterTemplateController::class, 'getDataset'])->name('app-master-templates.get-dataset');
    Route::resource('/settings/app-master-templates', AppMasterTemplateController::class);

    Route::get('/settings/menu-checklists', [MenuChecklistController::class, 'index'])->name('menu-checklists.index');
    Route::get('/settings/menu-checklists/export/{module}', [MenuChecklistController::class, 'export'])->name('menu-checklists.export');

    Route::get('/settings/log-activities', [LogActivityController::class, 'index'])->name('log-activities.index');
    Route::match(['GET', 'POST'], '/settings/log-activities/data', [LogActivityController::class, 'data'])->name('log-activities.data');
    Route::get('/settings/log-activities/{activity}/detail', [LogActivityController::class, 'detail'])->name('log-activities.detail');
    /** FRAMEWORK END */
});
