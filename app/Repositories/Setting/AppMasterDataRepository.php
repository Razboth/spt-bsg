<?php

namespace App\Repositories\Setting;

use App\Models\Setting\AppMasterData;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class AppMasterDataRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new AppMasterData());
    }
    public function getMasters(string $code): Builder
    {
        return $this->query()->where('app_master_category_code', $code);
    }
}
