<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\RecapModel;
use flight\Engine;

class RecapController
{
    protected Engine $app;
    protected RecapModel $recapModel;

    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->recapModel = new RecapModel($app);
    }

    public function index(): void
    {
        $this->app->render('pages/recap/index', [
            'title' => 'Récapitulatif',
            'headerTitle' => 'Récapitulatif',
            'pageTitle' => 'Récapitulatif',
        ], 'content');

        $this->app->render('layouts/base');
    }

    public function stats(): void
    {
        $stats = $this->recapModel->getMontantStats();

        $this->app->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }
}
