<?php

namespace App\Admin\Repositories;

use App\Models\V2rayNode as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class V2rayNode extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
