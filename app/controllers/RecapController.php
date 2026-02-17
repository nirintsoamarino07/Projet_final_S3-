<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\DashboardModel;
use flight\Engine;

class RecapController
{
    protected Engine $app;
    protected DashboardModel $dashboardModel;

    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->dashboardModel = new DashboardModel($app);
    }

    /**
     * Page de récapitulation (/recap)
     */
    public function index(): void
    {
        $recap = $this->dashboardModel->getRecapMontants();

        $this->app->render('pages/recap/index', [
            'title' => 'Récapitulatif',
            'headerTitle' => 'Récapitulatif',
            'pageTitle' => 'Récapitulatif des montants',
            'recap' => $recap,
        ], 'content');

        $this->app->render('layouts/base');
    }

    /**
     * Endpoint Ajax pour rafraîchir les données
     */
    public function refresh(): void
    {
        $recap = $this->dashboardModel->getRecapMontants();
        $this->app->json([
            'success' => true,
            'data' => $recap,
        ]);
    }
}
