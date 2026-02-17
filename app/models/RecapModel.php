<?php

declare(strict_types=1);

namespace app\models;

use flight\Engine;

class RecapModel
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    /**
     * @return array<string, float>
     */
    public function getMontantStats(): array
    {

        $besoinsTotalMontant = $this->sumMontantByType('besoin b', 'b.id_article', 'b.quantite_demandee');
        $besoinsSatisfaitsMontant = $this->sumMontantByType(
            'attribution at JOIN besoin b ON b.id_besoin = at.id_besoin',
            'b.id_article',
            'at.quantite_attribuee'
        );
        $donsRecusMontant = $this->sumMontantByType('don d', 'd.id_article', 'd.quantite_totale');
        $donsDispatcheMontant = $this->sumMontantByType('don d', 'd.id_article', 'd.quantite_distribuee');

        return [
            'besoins_totaux' => $besoinsTotalMontant,
            'besoins_satisfaits' => $besoinsSatisfaitsMontant,
            'dons_recus' => $donsRecusMontant,
            'dons_dispatche' => $donsDispatcheMontant,
        ];
    }

    private function sumMontantByType(string $fromSql, string $idArticleSql, string $quantiteSql): float{
        return (float) $this->app->db()->fetchField(
            'SELECT COALESCE(SUM(
                CASE
                    WHEN t.nom_type = "Argent" THEN ' . $quantiteSql . '
                    ELSE ' . $quantiteSql . ' * COALESCE(pu.prix, 0)
                END
            ), 0) AS montant
             FROM ' . $fromSql . '
             JOIN article a ON a.id_article = ' . $idArticleSql . '
             JOIN type_besoin t ON t.id_type = a.id_type
             LEFT JOIN prix_unitaire pu ON pu.id_article = ' . $idArticleSql . ' '
        );
    }
}