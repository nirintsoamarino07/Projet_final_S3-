<?php

declare(strict_types=1);

namespace app\models;

use flight\Engine;

class ArticleModel
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    /**
     * @return array<int, mixed>
     */
    public function getAllWithUnite(): array
    {
        return $this->app->db()->fetchAll(
            'SELECT a.id_article, a.nom_article, u.symbole
             FROM article a
             JOIN unite u ON u.id_unite = a.id_unite
             ORDER BY a.nom_article ASC'
        );
    }

    /**
     * @return array<int, mixed>
     */
    public function getAllWithUnitePrixUnitaire(): array
    {
        return $this->app->db()->fetchAll(
            'SELECT a.id_article, a.nom_article, u.symbole
             FROM article a
             JOIN unite u ON u.id_unite = a.id_unite
             JOIN type_besoin t ON t.id_type = a.id_type
             WHERE t.nom_type <> "Argent"
             ORDER BY a.nom_article ASC'
        );
    }
}
