<?php

namespace App\Admin\Repositories;

use App\Models\AdminRole as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class AdminRole extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
