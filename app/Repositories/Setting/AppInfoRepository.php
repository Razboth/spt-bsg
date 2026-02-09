<?php

namespace App\Repositories\Setting;

use App\Models\Setting\AppInfo;
use App\Models\Setting\SettingArea;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class AppInfoRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new AppInfo());
    }
    public function getInfo(): Builder
    {
        return $this->query();
    }

    public function createInfo(array $data): AppInfo
    {
        return $this->create($data);
    }

    public function updateInfo(array $data, int $id): AppInfo
    {
        return $this->update($data, $id);
    }
}
