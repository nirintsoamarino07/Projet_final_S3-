<?php

declare(strict_types=1);

namespace app\models;

use Exception;
use flight\Engine;

class PrixUnitaireModel
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
                pu.id_prix_unitaire,
                pu.id_article,
                pu.prix,
                pu.created_at,
                a.nom_article,
                t.nom_type,
                u.symbole
             FROM prix_unitaire pu
             JOIN article a ON a.id_article = pu.id_article
             JOIN type_besoin t ON t.id_type = a.id_type
             JOIN unite u ON u.id_unite = a.id_unite
             ORDER BY a.nom_article ASC, pu.id_prix_unitaire DESC'
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function getById(int $idPrixUnitaire): array
    {
        $row = $this->app->db()->fetchRow(
            'SELECT id_prix_unitaire, id_article, prix, created_at
             FROM prix_unitaire
             WHERE id_prix_unitaire = ?',
            [ $idPrixUnitaire ]
        );

        if (empty($row) || empty($row->id_prix_unitaire)) {
            throw new Exception('Prix unitaire introuvable.');
        }

        return [
            'id_prix_unitaire' => (int) $row->id_prix_unitaire,
            'id_article' => (int) $row->id_article,
            'prix' => (float) $row->prix,
            'created_at' => (string) $row->created_at,
        ];
    }

    public function create(int $idArticle, float $prix): int
    {
        $this->assertArticleEligible($idArticle);
        $this->assertUniqueArticle($idArticle, null);

        $this->app->db()->runQuery(
            'INSERT INTO prix_unitaire (id_article, prix) VALUES (?, ?)',
            [ $idArticle, $prix ]
        );

        return (int) $this->app->db()->lastInsertId();
    }

    public function update(int $idPrixUnitaire, int $idArticle, float $prix): void
    {
        $this->assertArticleEligible($idArticle);
        $this->assertUniqueArticle($idArticle, $idPrixUnitaire);

        $this->app->db()->runQuery(
            'UPDATE prix_unitaire SET id_article = ?, prix = ? WHERE id_prix_unitaire = ?',
            [ $idArticle, $prix, $idPrixUnitaire ]
        );
    }

    public function delete(int $idPrixUnitaire): void
    {
        $this->app->db()->runQuery(
            'DELETE FROM prix_unitaire WHERE id_prix_unitaire = ?',
            [ $idPrixUnitaire ]
        );
    }

    private function assertUniqueArticle(int $idArticle, ?int $excludeIdPrixUnitaire): void
    {
        $params = [ $idArticle ];
        $sql = 'SELECT COUNT(*) AS c FROM prix_unitaire WHERE id_article = ?';

        if ($excludeIdPrixUnitaire !== null) {
            $sql .= ' AND id_prix_unitaire <> ?';
            $params[] = $excludeIdPrixUnitaire;
        }

        $row = $this->app->db()->fetchRow($sql, $params);
        $count = !empty($row) ? (int) $row->c : 0;
        if ($count > 0) {
            throw new Exception('Un prix unitaire existe déjà pour cet article.');
        }
    }

    private function assertArticleEligible(int $idArticle): void
    {
        $row = $this->app->db()->fetchRow(
            'SELECT a.id_article, t.nom_type
             FROM article a
             JOIN type_besoin t ON t.id_type = a.id_type
             WHERE a.id_article = ?',
            [ $idArticle ]
        );

        if (empty($row) || empty($row->id_article)) {
            throw new Exception('Article introuvable.');
        }

        if ((string) $row->nom_type === 'Argent') {
            throw new Exception('Le prix unitaire ne s\'applique pas aux articles de type Argent.');
        }
    }
}
