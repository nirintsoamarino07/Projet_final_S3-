<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\DashboardModel;
use app\models\EvenementModel;
use app\models\RegionModel;
use app\models\TypeBesoinModel;
use app\models\VilleModel;
use flight\Engine;

class DashboardController
{
    protected Engine $app;
    protected DashboardModel $dashboardModel;
    protected RegionModel $regionModel;
    protected VilleModel $villeModel;
    protected TypeBesoinModel $typeBesoinModel;
    protected EvenementModel $evenementModel;

    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->dashboardModel = new DashboardModel($app);
        $this->regionModel = new RegionModel($app);
        $this->villeModel = new VilleModel($app);
        $this->typeBesoinModel = new TypeBesoinModel($app);
        $this->evenementModel = new EvenementModel($app);
    }

    /**
     * Dashboard page (/)
     */
    public function index(): void
    {
        $filters = [
            'id_region' => !empty($_GET['id_region']) ? (int) $_GET['id_region'] : null,
            'id_ville' => !empty($_GET['id_ville']) ? (int) $_GET['id_ville'] : null,
            'id_type' => !empty($_GET['id_type']) ? (int) $_GET['id_type'] : null,
            'id_evenement' => !empty($_GET['id_evenement']) ? (int) $_GET['id_evenement'] : null,
        ];

        $besoins = $this->dashboardModel->listBesoinsWithCoverage($filters);
        $stats = $this->dashboardModel->getStats($filters);
        $achatsParVille = $this->dashboardModel->getAchatMontantsParVille($filters);

        $regions = $this->regionModel->getAll();
        $villes = $this->villeModel->getAll();
        $types = $this->typeBesoinModel->getAll();
        $evenements = $this->evenementModel->getAll();

        $this->app->render('pages/dashboard/index', [
            'title' => 'Dashboard',
            'headerTitle' => 'Dashboard',
            'pageTitle' => 'Tableau de bord',
            'filters' => $filters,
            'regions' => $regions,
            'villes' => $villes,
            'types' => $types,
            'evenements' => $evenements,
            'stats' => $stats,
            'achatsParVille' => $achatsParVille,
            'besoins' => $besoins,
        ], 'content');

        $this->app->render('layouts/base');
    }

    public function attributions(int $idBesoin): void
    {
        $rows = $this->dashboardModel->listAttributionsByBesoin($idBesoin);

        $this->app->json([
            'success' => true,
            'items' => array_map(function($r) {
                return [
                    'id_attribution' => (int) $r->id_attribution,
                    'quantite_attribuee' => (float) $r->quantite_attribuee,
                    'date_attribution' => (string) $r->date_attribution,
                    'id_don' => (int) $r->id_don,
                    'donateur' => (string) ($r->donateur ?? ''),
                    'source' => (string) ($r->source ?? ''),
                ];
            }, $rows),
        ]);
    }
}
