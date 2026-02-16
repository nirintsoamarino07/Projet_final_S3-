<?php

declare(strict_types=1);

namespace app\models;

use flight\Engine;

class RegionModel
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    /**
     * @return array<int, mixed>
     */
    public function getAll(): array
    {
        return $this->app->db()->fetchAll(
            'SELECT id_region, nom_region FROM region ORDER BY nom_region ASC'
        );
    }
}
