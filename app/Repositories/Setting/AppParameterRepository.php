<?php

namespace App\Repositories\Setting;

use App\Repositories\BaseRepository;

class AppParameterRepository extends BaseRepository
{
    public function __construct($model)
    {
        parent::__construct($model);
    }

    public function getByCode(string $code)
    {
        return $this->model->where('code', $code)->first();
    }
}
