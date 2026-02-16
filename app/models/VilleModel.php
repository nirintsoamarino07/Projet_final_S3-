<?php

declare(strict_types=1);

namespace app\models;

use flight\Engine;

class VilleModel
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
            'SELECT id_ville, nom_ville FROM ville ORDER BY nom_ville ASC'
        );
    }

    public function create(string $nomVille, int $idRegion): int
    {
        $this->app->db()->runQuery(
            'INSERT INTO ville (nom_ville, id_region) VALUES (?, ?)',
            [ $nomVille, $idRegion ]
        );

        return (int) $this->app->db()->lastInsertId();
    }
}
