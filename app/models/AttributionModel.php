<?php

declare(strict_types=1);

namespace app\models;

use Exception;
use flight\Engine;

class AttributionModel
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    /**
     * @return array<int, mixed>
     */
    public function listBesoinsWithCoverage(): array
    {
        return $this->app->db()->fetchAll(
            'SELECT
                b.id_besoin,
                b.id_article,
                b.quantite_demandee,
                b.date_saisie,
                v.nom_ville,
                a.nom_article,
                u.symbole,
                COALESCE(SUM(at.quantite_attribuee), 0) AS quantite_attribuee,
                (b.quantite_demandee - COALESCE(SUM(at.quantite_attribuee), 0)) AS quantite_restante
             FROM besoin b
             JOIN ville v ON v.id_ville = b.id_ville
             JOIN article a ON a.id_article = b.id_article
             JOIN unite u ON u.id_unite = a.id_unite
             LEFT JOIN attribution at ON at.id_besoin = b.id_besoin
             GROUP BY b.id_besoin, b.id_article, b.quantite_demandee, b.date_saisie, v.nom_ville, a.nom_article, u.symbole
             ORDER BY b.date_saisie DESC, b.id_besoin DESC'
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function getBesoinDetails(int $idBesoin): array
    {
        $row = $this->app->db()->fetchRow(
            'SELECT
                b.id_besoin,
                b.id_article,
                b.quantite_demandee,
                v.nom_ville,
                a.nom_article,
                u.symbole,
                COALESCE(SUM(at.quantite_attribuee), 0) AS quantite_attribuee,
                (b.quantite_demandee - COALESCE(SUM(at.quantite_attribuee), 0)) AS quantite_restante
             FROM besoin b
             JOIN ville v ON v.id_ville = b.id_ville
             JOIN article a ON a.id_article = b.id_article
             JOIN unite u ON u.id_unite = a.id_unite
             LEFT JOIN attribution at ON at.id_besoin = b.id_besoin
             WHERE b.id_besoin = ?
             GROUP BY b.id_besoin, b.id_article, b.quantite_demandee, v.nom_ville, a.nom_article, u.symbole',
            [ $idBesoin ]
        );

        if (empty($row) || empty($row->id_besoin)) {
            throw new Exception('Besoin introuvable.');
        }

        return [
            'id_besoin' => (int) $row->id_besoin,
            'id_article' => (int) $row->id_article,
            'nom_ville' => (string) $row->nom_ville,
            'nom_article' => (string) $row->nom_article,
            'symbole' => (string) $row->symbole,
            'quantite_demandee' => (float) $row->quantite_demandee,
            'quantite_attribuee' => (float) $row->quantite_attribuee,
            'quantite_restante' => (float) $row->quantite_restante,
        ];
    }

    /**
     * @return array<int, mixed>
     */
    public function listDonsDisponiblesByArticle(int $idArticle): array
    {
        return $this->app->db()->fetchAll(
            'SELECT
                d.id_don,
                d.quantite_totale,
                d.quantite_distribuee,
                (d.quantite_totale - d.quantite_distribuee) AS quantite_disponible,
                d.date_reception,
                d.donateur,
                d.source
             FROM don d
             WHERE d.id_article = ?
               AND (d.quantite_totale - d.quantite_distribuee) > 0
             ORDER BY d.date_reception ASC, d.id_don ASC',
            [ $idArticle ]
        );
    }

    /**
     * @return array<int, mixed>
     */
    public function listDonsArgentDisponibles(): array
    {
        return $this->app->db()->fetchAll(
            'SELECT
                d.id_don,
                d.id_article,
                d.quantite_totale,
                d.quantite_distribuee,
                (d.quantite_totale - d.quantite_distribuee) AS montant_disponible,
                d.date_reception,
                d.donateur,
                d.source
             FROM don d
             JOIN article a ON a.id_article = d.id_article
             JOIN type_besoin t ON t.id_type = a.id_type
             WHERE t.nom_type = "Argent"
               AND (d.quantite_totale - d.quantite_distribuee) > 0
             ORDER BY d.date_reception ASC, d.id_don ASC'
        );
    }

    public function getPrixUnitaireByArticle(int $idArticle): ?float
    {
        $row = $this->app->db()->fetchRow(
            'SELECT prix FROM prix_unitaire WHERE id_article = ?',
            [ $idArticle ]
        );

        if (empty($row)) {
            return null;
        }

        return isset($row->prix) ? (float) $row->prix : null;
    }

    public function resetDispatch(): void
    {
        $db = $this->app->db();

        try {
            $db->beginTransaction();

            $db->runQuery('DELETE FROM attribution');
            $db->runQuery('DELETE FROM conversion_argent');

            $db->runQuery(
                'DELETE FROM don WHERE source IN ("Conversion argent", "Vente matériel")'
            );

            $db->runQuery('UPDATE don SET quantite_distribuee = 0');

            $db->commit();
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    public function createAttributionFifo(int $idBesoin, float $quantite): void
    {
        if ($quantite <= 0) {
            throw new Exception('Quantité invalide.');
        }

        $db = $this->app->db();

        try {
            $db->beginTransaction();

            $besoin = $this->getBesoinDetails($idBesoin);
            if ($besoin['quantite_restante'] <= 0) {
                throw new Exception('Ce besoin est déjà couvert.');
            }
            if ($quantite > $besoin['quantite_restante']) {
                throw new Exception('La quantité dépasse le reste à couvrir pour ce besoin.');
            }

            $dons = $db->fetchAll(
                'SELECT id_don, id_article, quantite_totale, quantite_distribuee
                 FROM don
                 WHERE id_article = ?
                   AND (quantite_totale - quantite_distribuee) > 0
                 ORDER BY date_reception ASC, id_don ASC
                 FOR UPDATE',
                [ (int) $besoin['id_article'] ]
            );

            $totalDisponible = 0.0;
            foreach ($dons as $d) {
                $totalDisponible += (float) $d->quantite_totale - (float) $d->quantite_distribuee;
            }

            if ($quantite > $totalDisponible) {
                throw new Exception('La quantité dépasse le stock disponible des dons.');
            }

            $reste = $quantite;
            foreach ($dons as $don) {
                if ($reste <= 0) {
                    break;
                }

                $disponible = (float) $don->quantite_totale - (float) $don->quantite_distribuee;
                if ($disponible <= 0) {
                    continue;
                }

                $aPrendre = $reste <= $disponible ? $reste : $disponible;

                $db->runQuery(
                    'INSERT INTO attribution (id_besoin, id_don, quantite_attribuee)
                     VALUES (?, ?, ?)',
                    [ $idBesoin, (int) $don->id_don, (float) $aPrendre ]
                );

                $db->runQuery(
                    'UPDATE don SET quantite_distribuee = quantite_distribuee + ? WHERE id_don = ?',
                    [ (float) $aPrendre, (int) $don->id_don ]
                );

                $reste -= $aPrendre;
            }

            if ($reste > 0) {
                throw new Exception('Stock insuffisant pour compléter l\'attribution.');
            }

            $db->commit();
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    public function createAttributionFromArgentFifo(int $idBesoin, float $quantite): void
    {
        if ($quantite <= 0) {
            throw new Exception('Quantité invalide.');
        }

        $db = $this->app->db();

        try {
            $db->beginTransaction();

            $besoin = $this->getBesoinDetails($idBesoin);
            if ($besoin['quantite_restante'] <= 0) {
                throw new Exception('Ce besoin est déjà couvert.');
            }
            if ($quantite > $besoin['quantite_restante']) {
                throw new Exception('La quantité dépasse le reste à couvrir pour ce besoin.');
            }

            $prixUnitaire = $this->getPrixUnitaireByArticle((int) $besoin['id_article']);
            if ($prixUnitaire === null || $prixUnitaire <= 0) {
                throw new Exception('Prix unitaire introuvable pour cet article.');
            }

            $montantRequis = $quantite * $prixUnitaire;
            if ($montantRequis <= 0) {
                throw new Exception('Montant requis invalide.');
            }

            $donsArgent = $db->fetchAll(
                'SELECT d.id_don, d.quantite_totale, d.quantite_distribuee
                 FROM don d
                 JOIN article a ON a.id_article = d.id_article
                 JOIN type_besoin t ON t.id_type = a.id_type
                 WHERE t.nom_type = "Argent"
                   AND (d.quantite_totale - d.quantite_distribuee) > 0
                 ORDER BY d.date_reception ASC, d.id_don ASC
                 FOR UPDATE'
            );

            $totalArgentDisponible = 0.0;
            foreach ($donsArgent as $d) {
                $totalArgentDisponible += (float) $d->quantite_totale - (float) $d->quantite_distribuee;
            }

            if ($montantRequis > $totalArgentDisponible) {
                throw new Exception('Montant insuffisant dans les dons en argent.');
            }

            $resteMontant = $montantRequis;
            foreach ($donsArgent as $donArgent) {
                if ($resteMontant <= 0) {
                    break;
                }

                $dispo = (float) $donArgent->quantite_totale - (float) $donArgent->quantite_distribuee;
                if ($dispo <= 0) {
                    continue;
                }

                $montantUtilise = $resteMontant <= $dispo ? $resteMontant : $dispo;
                $qteObtenue = $montantUtilise / $prixUnitaire;

                $db->runQuery(
                    'INSERT INTO conversion_argent (id_don_argent, id_article_cible, montant_utilise, prix_unitaire, quantite_obtenue)
                     VALUES (?, ?, ?, ?, ?)',
                    [ (int) $donArgent->id_don, (int) $besoin['id_article'], (float) $montantUtilise, (float) $prixUnitaire, (float) $qteObtenue ]
                );

                $db->runQuery(
                    'UPDATE don SET quantite_distribuee = quantite_distribuee + ? WHERE id_don = ?',
                    [ (float) $montantUtilise, (int) $donArgent->id_don ]
                );

                $resteMontant -= $montantUtilise;
            }

            if ($resteMontant > 0) {
                throw new Exception('Montant insuffisant pour compléter la conversion.');
            }

            $db->runQuery(
                'INSERT INTO don (id_article, quantite_totale, quantite_distribuee, donateur, source, observations)
                 VALUES (?, ?, ?, ?, ?, ?)',
                [ (int) $besoin['id_article'], (float) $quantite, (float) $quantite, null, 'Conversion argent', 'Conversion automatique depuis don(s) en argent' ]
            );
            $idDonConverti = (int) $db->lastInsertId();

            $db->runQuery(
                'INSERT INTO attribution (id_besoin, id_don, quantite_attribuee, observations)
                 VALUES (?, ?, ?, ?)',
                [ $idBesoin, $idDonConverti, (float) $quantite, 'Attribution via conversion argent' ]
            );

            $db->commit();
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    public function createAttribution(int $idBesoin, int $idDon, float $quantite): void
    {
        if ($quantite <= 0) {
            throw new Exception('Quantité invalide.');
        }

        $db = $this->app->db();

        try {
            $db->beginTransaction();

            $besoin = $this->getBesoinDetails($idBesoin);
            if ($besoin['quantite_restante'] <= 0) {
                throw new Exception('Ce besoin est déjà couvert.');
            }
            if ($quantite > $besoin['quantite_restante']) {
                throw new Exception('La quantité dépasse le reste à couvrir pour ce besoin.');
            }

            $dons = $db->fetchAll(
                'SELECT id_don, id_article, quantite_totale, quantite_distribuee
                 FROM don
                 WHERE id_don = ?
                 LIMIT 1 FOR UPDATE',
                [ $idDon ]
            );
            $don = $dons[0] ?? null;

            if (empty($don) || empty($don->id_don)) {
                throw new Exception('Don introuvable.');
            }

            if ((int) $don->id_article !== (int) $besoin['id_article']) {
                throw new Exception('Le don sélectionné ne correspond pas à l’article du besoin.');
            }

            $disponible = (float) $don->quantite_totale - (float) $don->quantite_distribuee;
            if ($quantite > $disponible) {
                throw new Exception('La quantité dépasse le stock disponible du don.');
            }

            $db->runQuery(
                'INSERT INTO attribution (id_besoin, id_don, quantite_attribuee)
                 VALUES (?, ?, ?)',
                [ $idBesoin, $idDon, $quantite ]
            );

            $db->runQuery(
                'UPDATE don SET quantite_distribuee = quantite_distribuee + ? WHERE id_don = ?',
                [ $quantite, $idDon ]
            );

            $db->commit();
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }
}
