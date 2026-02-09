<?php

namespace App\Services\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppCompanyRequest;
use App\Repositories\Setting\AppCompanyRepository;
use App\Models\Setting\AppCompany;
use Exception;
use Illuminate\Support\Facades\Cache;

class AppCompanyService extends Controller
{
    private AppCompanyRepository $appCompanyRepository;

    public function __construct()
    {
        $this->appCompanyRepository = new AppCompanyRepository(
            new AppCompany()
        );
    }

    public function getCompany(): AppCompany
    {
        return $this->appCompanyRepository->first() ?? new AppCompany();
    }

    public function saveCompany(AppCompanyRequest $request): void
    {
        $fields = [
            'name' => $request->name,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'fax' => $request->fax,
            'email' => $request->email,
            'website' => $request->website,
            'description' => $request->description,
        ];

        Cache::forget('app_company');

        $company = $this->getCompany();

        if ($company->id !== null) {
            $this->appCompanyRepository->update($fields, $company->id);
        } else {
            $company = $this->appCompanyRepository->create($fields);
        }

        Cache::forever('app_company', $company);

        defaultUploadFile($company, $request, AppCompany::LOGO_PATH, 'company_logo_' . time(), 'logo', false);
    }
}
