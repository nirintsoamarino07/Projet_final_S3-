<?php

declare(strict_types=1);

namespace app\models;

use flight\Engine;

class BesoinModel
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    /**
     * @return array<int, mixed>
     */
    public function listAll(): array
    {
        return $this->app->db()->fetchAll(
            'SELECT 
                b.id_besoin,
                b.quantite_demandee,
                b.date_saisie,
                v.nom_ville,
                a.nom_article,
                u.symbole
             FROM besoin b
             JOIN ville v ON v.id_ville = b.id_ville
             JOIN article a ON a.id_article = b.id_article
             JOIN unite u ON u.id_unite = a.id_unite
             ORDER BY b.date_saisie DESC, b.id_besoin DESC'
        );
    }

    public function create(int $idVille, int $idArticle, float $quantite, ?string $observations = null): int
    {
        $this->app->db()->runQuery(
            'INSERT INTO besoin (id_ville, id_article, quantite_demandee, observations)
             VALUES (?, ?, ?, ?)',
            [ $idVille, $idArticle, $quantite, $observations ]
        );

        return (int) $this->app->db()->lastInsertId();
    }
}
