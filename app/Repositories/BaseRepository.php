<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;

class BaseRepository
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function query(): Builder
    {
        return $this->model->query();
    }

    public function get()
    {
        return $this->model->get();
    }

    public function first()
    {
        return $this->model->first();
    }

    public function getById(string $id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function createMany(array $data)
    {
        return $this->model->insert($data);
    }

    public function update(array $data, string $id)
    {
        $model = $this->model->find($id);
        $model->update($data);

        return $model;
    }

    public function updateOrCreate(array $data, array $condition)
    {
        return $this->model->updateOrCreate($condition, $data);
    }

    public function destroy(string $id): void
    {
        $this->model->destroy($id);
    }
}
