<?php

declare(strict_types=1);

namespace app\models;

use flight\Engine;

class EvenementModel
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
            'SELECT id_evenement, nom_evenement FROM evenement ORDER BY date_debut DESC, id_evenement DESC'
        );
    }
}
