<?php

declare(strict_types=1);

namespace app\models;

use flight\Engine;

class TypeBesoinModel
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
            'SELECT id_type, nom_type FROM type_besoin ORDER BY nom_type ASC'
        );
    }
}
