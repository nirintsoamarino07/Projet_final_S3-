<?php

declare(strict_types=1);

namespace app\models;

use flight\Engine;

class DonModel
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
                d.id_don,
                d.quantite_totale,
                d.quantite_distribuee,
                d.date_reception,
                d.donateur,
                d.source,
                a.nom_article,
                u.symbole
             FROM don d
             JOIN article a ON a.id_article = d.id_article
             JOIN unite u ON u.id_unite = a.id_unite
             ORDER BY d.date_reception DESC, d.id_don DESC'
        );
    }

    public function create(int $idArticle, float $quantiteTotale, ?string $donateur = null, ?string $source = null, ?string $observations = null): int
    {
        $this->app->db()->runQuery(
            'INSERT INTO don (id_article, quantite_totale, donateur, source, observations)
             VALUES (?, ?, ?, ?, ?)',
            [ $idArticle, $quantiteTotale, $donateur, $source, $observations ]
        );

        return (int) $this->app->db()->lastInsertId();
    }
}
