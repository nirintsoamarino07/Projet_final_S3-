<?php

declare(strict_types=1);

namespace app\models;

use flight\Engine;

class DashboardModel
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    /**
     * @param array<string, int|string|null> $filters
     * @return array<int, mixed>
     */
    public function listBesoinsWithCoverage(array $filters): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['id_region'])) {
            $where[] = 'r.id_region = ?';
            $params[] = (int) $filters['id_region'];
        }
        if (!empty($filters['id_ville'])) {
            $where[] = 'v.id_ville = ?';
            $params[] = (int) $filters['id_ville'];
        }
        if (!empty($filters['id_type'])) {
            $where[] = 't.id_type = ?';
            $params[] = (int) $filters['id_type'];
        }
        if (!empty($filters['id_evenement'])) {
            $where[] = 'b.id_evenement = ?';
            $params[] = (int) $filters['id_evenement'];
        }

        $whereSql = $where !== [] ? ('WHERE ' . implode(' AND ', $where)) : '';

        return $this->app->db()->fetchAll(
            'SELECT
                b.id_besoin,
                b.id_evenement,
                b.quantite_demandee,
                b.date_saisie,
                v.id_ville,
                v.nom_ville,
                r.id_region,
                r.nom_region,
                a.id_article,
                a.nom_article,
                t.id_type,
                t.nom_type,
                u.symbole,
                COALESCE(SUM(at.quantite_attribuee), 0) AS quantite_attribuee,
                (b.quantite_demandee - COALESCE(SUM(at.quantite_attribuee), 0)) AS quantite_restante
             FROM besoin b
             JOIN ville v ON v.id_ville = b.id_ville
             JOIN region r ON r.id_region = v.id_region
             JOIN article a ON a.id_article = b.id_article
             JOIN type_besoin t ON t.id_type = a.id_type
             JOIN unite u ON u.id_unite = a.id_unite
             LEFT JOIN attribution at ON at.id_besoin = b.id_besoin
             ' . $whereSql . '
             GROUP BY b.id_besoin, b.id_evenement, b.quantite_demandee, b.date_saisie,
                      v.id_ville, v.nom_ville, r.id_region, r.nom_region,
                      a.id_article, a.nom_article, t.id_type, t.nom_type, u.symbole
             HAVING (b.quantite_demandee - COALESCE(SUM(at.quantite_attribuee), 0)) > 0
             ORDER BY quantite_restante DESC, b.date_saisie DESC, b.id_besoin DESC',
            $params
        );
    }

    /**
     * @param array<string, int|string|null> $filters
     * @return array<string, float|int>
     */
    public function getStats(array $filters): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['id_region'])) {
            $where[] = 'r.id_region = ?';
            $params[] = (int) $filters['id_region'];
        }
        if (!empty($filters['id_ville'])) {
            $where[] = 'v.id_ville = ?';
            $params[] = (int) $filters['id_ville'];
        }
        if (!empty($filters['id_type'])) {
            $where[] = 't.id_type = ?';
            $params[] = (int) $filters['id_type'];
        }
        if (!empty($filters['id_evenement'])) {
            $where[] = 'b.id_evenement = ?';
            $params[] = (int) $filters['id_evenement'];
        }

        $whereSql = $where !== [] ? ('WHERE ' . implode(' AND ', $where)) : '';

        $rows = $this->app->db()->fetchAll(
            'SELECT
                v.id_ville,
                b.quantite_demandee,
                COALESCE(SUM(at.quantite_attribuee), 0) AS quantite_attribuee,
                (b.quantite_demandee - COALESCE(SUM(at.quantite_attribuee), 0)) AS quantite_restante
             FROM besoin b
             JOIN ville v ON v.id_ville = b.id_ville
             JOIN region r ON r.id_region = v.id_region
             JOIN article a ON a.id_article = b.id_article
             JOIN type_besoin t ON t.id_type = a.id_type
             LEFT JOIN attribution at ON at.id_besoin = b.id_besoin
             ' . $whereSql . '
             GROUP BY v.id_ville, b.id_besoin, b.quantite_demandee',
            $params
        );

        $villes = [];
        $besoinsTotal = 0;
        $besoinsCouvert = 0;
        $besoinsACouvrir = 0;

        foreach ($rows as $r) {
            $besoinsTotal++;
            $villes[(string) $r->id_ville] = true;
            if ((float) $r->quantite_restante <= 0.0) {
                $besoinsCouvert++;
            } else {
                $besoinsACouvrir++;
            }
        }

        $donsDisponibles = (int) $this->app->db()->fetchField(
            'SELECT COUNT(*) FROM don WHERE (quantite_totale - quantite_distribuee) > 0'
        );

        $pourcentage = $besoinsTotal > 0 ? round(($besoinsCouvert / $besoinsTotal) * 100, 2) : 0.0;

        return [
            'villes_aidees' => count($villes),
            'besoins_total' => $besoinsTotal,
            'besoins_couverts' => $besoinsCouvert,
            'besoins_a_couvrir' => $besoinsACouvrir,
            'pourcentage_couverture' => $pourcentage,
            'dons_disponibles' => $donsDisponibles,
        ];
    }

    /**
     * @param array<string, int|string|null> $filters
     * @return array<int, mixed>
     */
    public function getAchatMontantsParVille(array $filters): array
    {
        $where = [
            'd.source = "Conversion argent"',
        ];
        $params = [];

        if (!empty($filters['id_region'])) {
            $where[] = 'r.id_region = ?';
            $params[] = (int) $filters['id_region'];
        }
        if (!empty($filters['id_ville'])) {
            $where[] = 'v.id_ville = ?';
            $params[] = (int) $filters['id_ville'];
        }
        if (!empty($filters['id_type'])) {
            $where[] = 't.id_type = ?';
            $params[] = (int) $filters['id_type'];
        }
        if (!empty($filters['id_evenement'])) {
            $where[] = 'b.id_evenement = ?';
            $params[] = (int) $filters['id_evenement'];
        }

        $whereSql = $where !== [] ? ('WHERE ' . implode(' AND ', $where)) : '';

        return $this->app->db()->fetchAll(
            'SELECT
                v.id_ville,
                v.nom_ville,
                COALESCE(SUM(at.quantite_attribuee * pu.prix), 0) AS montant_achat
             FROM attribution at
             JOIN don d ON d.id_don = at.id_don
             JOIN besoin b ON b.id_besoin = at.id_besoin
             JOIN ville v ON v.id_ville = b.id_ville
             JOIN region r ON r.id_region = v.id_region
             JOIN article a ON a.id_article = b.id_article
             JOIN type_besoin t ON t.id_type = a.id_type
             JOIN prix_unitaire pu ON pu.id_article = b.id_article
             ' . $whereSql . '
             GROUP BY v.id_ville, v.nom_ville
             ORDER BY montant_achat DESC, v.nom_ville ASC',
            $params
        );
    }

    /**
     * @return array<int, mixed>
     */
    public function listAttributionsByBesoin(int $idBesoin): array
    {
        return $this->app->db()->fetchAll(
            'SELECT
                at.id_attribution,
                at.quantite_attribuee,
                at.date_attribution,
                d.id_don,
                d.donateur,
                d.source
             FROM attribution at
             JOIN don d ON d.id_don = at.id_don
             WHERE at.id_besoin = ?
             ORDER BY at.date_attribution DESC, at.id_attribution DESC',
            [ $idBesoin ]
        );
    }
}
