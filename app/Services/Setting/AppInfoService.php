<?php

namespace App\Services\Setting;

use Alert;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppInfoRequest;
use App\Repositories\Setting\AppInfoRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
class AppInfoService extends Controller
{

    private AppInfoRepository $appInfoRepository;
    public string $infoPath;
    public function __construct()
    {
        $this->appInfoRepository = new AppInfoRepository();
        $this->infoPath = '/uploads/setting/info/';
    }

    public function getInfo() : array|Collection
    {
        return $this->appInfoRepository->getInfo()->get();
    }

    public function saveInfo(AppInfoRequest $request): void
    {
        $fields = [
            'title' => $request->input('title'),
            'primary_color' => $request->input('primary_color'),
            'light_primary_color' => $request->input('light_primary_color'),
            'background_light_primary_color' => $request->input('background_light_primary_color'),
            'app_version' => $request->input('app_version'),
            'year' => $request->input('year'),
            'footer_text' => $request->input('footer_text'),
            'login_page_title' => $request->input('login_page_title'),
            'login_page_subtitle' => $request->input('login_page_subtitle'),
            'login_page_description' => $request->input('login_page_description'),
            'location_log_status' => $request->input('location_log_status'),
        ];

        try {
            Cache::forget('app_info');

            $info = $this->getInfo()->first();
            if($info){
                $this->appInfoRepository->update($fields, $info->id);
            }else{
                $info = $this->appInfoRepository->create($fields);
            }

            defaultUploadFile($info, $request, $this->infoPath, 'login_page_image_'. time(), 'login_page_image', false);
            defaultUploadFile($info, $request, $this->infoPath, 'login_page_logo_'. time(), 'login_page_logo', false);
            defaultUploadFile($info, $request, $this->infoPath, 'login_page_background_image_'. time(), 'login_page_background_image', false);
            defaultUploadFile($info, $request, $this->infoPath, 'app_logo_'. time(), 'app_logo', false);
            defaultUploadFile($info, $request, $this->infoPath, 'app_logo_small_'. time(), 'app_logo_small', false);
            defaultUploadFile($info, $request, $this->infoPath, 'app_icon_'. time(), 'app_icon', false);

            Alert::success('Berhasil', 'Data berhasil disimpan');
        } catch (Exception $e) {
            Alert::error('Gagal', 'Data gagal disimpan '.$e->getMessage());
        }
    }
}
