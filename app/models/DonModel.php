<?php

declare(strict_types=1);

namespace app\models;

use Exception;
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
                d.id_article,
                d.quantite_totale,
                d.quantite_distribuee,
                d.date_reception,
                d.donateur,
                d.source,
                a.nom_article,
                t.nom_type,
                u.symbole
             FROM don d
             JOIN article a ON a.id_article = d.id_article
             JOIN type_besoin t ON t.id_type = a.id_type
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

    public function vendreMaterielEnArgent(int $idDonMateriel, float $quantite, float $reductionPercent): int
    {
        if ($idDonMateriel <= 0) {
            throw new Exception('Don introuvable.');
        }
        if ($quantite <= 0) {
            throw new Exception('Quantité invalide.');
        }
        if ($reductionPercent < 0 || $reductionPercent >= 100) {
            throw new Exception('Pourcentage invalide.');
        }

        $db = $this->app->db();

        try {
            $db->beginTransaction();

            $dons = $db->fetchAll(
                'SELECT d.id_don, d.id_article, d.quantite_totale, d.quantite_distribuee, t.nom_type
                 FROM don d
                 JOIN article a ON a.id_article = d.id_article
                 JOIN type_besoin t ON t.id_type = a.id_type
                 WHERE d.id_don = ?
                 LIMIT 1 FOR UPDATE',
                [ $idDonMateriel ]
            );
            $don = $dons[0] ?? null;

            if (empty($don) || empty($don->id_don)) {
                throw new Exception('Don introuvable.');
            }
            if ((string) $don->nom_type === 'Argent') {
                throw new Exception('Impossible de vendre un don en argent.');
            }

            $disponible = (float) $don->quantite_totale - (float) $don->quantite_distribuee;
            if ($quantite > $disponible) {
                throw new Exception('Stock insuffisant pour vendre.');
            }

            $rowBesoin = $db->fetchRow(
                'SELECT
                    COALESCE(SUM(b.quantite_demandee), 0) AS demande,
                    COALESCE(SUM(at.quantite_attribuee), 0) AS attribue
                 FROM besoin b
                 LEFT JOIN attribution at ON at.id_besoin = b.id_besoin
                 WHERE b.id_article = ?',
                [ (int) $don->id_article ]
            );
            $demande = !empty($rowBesoin) ? (float) $rowBesoin->demande : 0.0;
            $attribue = !empty($rowBesoin) ? (float) $rowBesoin->attribue : 0.0;
            $reste = $demande - $attribue;
            if ($reste > 0) {
                throw new Exception('On ne peut pas vendre');
            }

            $prix = (float) $db->fetchField(
                'SELECT prix FROM prix_unitaire WHERE id_article = ?',
                [ (int) $don->id_article ]
            );
            if ($prix <= 0) {
                throw new Exception('Prix unitaire introuvable pour cet article.');
            }

            $montantBrut = $quantite * $prix;
            $montantVente = $montantBrut * (1 - ($reductionPercent / 100));
            if ($montantVente <= 0) {
                throw new Exception('Montant de vente invalide.');
            }

            $idArticleArgent = (int) $db->fetchField(
                'SELECT MIN(a.id_article)
                 FROM article a
                 JOIN type_besoin t ON t.id_type = a.id_type
                 WHERE t.nom_type = "Argent"'
            );
            if ($idArticleArgent <= 0) {
                throw new Exception('Article Argent introuvable.');
            }

            $db->runQuery(
                'UPDATE don SET quantite_distribuee = quantite_distribuee + ? WHERE id_don = ?',
                [ (float) $quantite, (int) $don->id_don ]
            );

            $db->runQuery(
                'INSERT INTO don (id_article, quantite_totale, quantite_distribuee, donateur, source, observations)
                 VALUES (?, ?, ?, ?, ?, ?)',
                [ $idArticleArgent, (float) $montantVente, 0.0, null, 'Vente matériel', 'Vente du don #' . (int) $don->id_don . ' (qte: ' . (float) $quantite . ', reduction: ' . (float) $reductionPercent . '%)' ]
            );

            $idDonArgent = (int) $db->lastInsertId();

            $db->commit();
            return $idDonArgent;
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }
}
