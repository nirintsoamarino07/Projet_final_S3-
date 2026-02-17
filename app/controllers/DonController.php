<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\ArticleModel;
use app\models\DonModel;
use flight\Engine;

class DonController
{
    protected Engine $app;
    protected DonModel $donModel;
    protected ArticleModel $articleModel;

    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->donModel = new DonModel($app);
        $this->articleModel = new ArticleModel($app);
    }

    public function index(): void
    {
        $dons = $this->donModel->listAll();

        $this->app->render('pages/dons/list', [
            'title' => 'Dons - Liste',
            'headerTitle' => 'Dons',
            'pageTitle' => 'Liste des dons',
            'dons' => $dons,
        ], 'content');

        $this->app->render('layouts/base');
    }

    public function createForm(): void
    {
        $articles = $this->articleModel->getAllWithUnite();

        $this->app->render('pages/dons/form', [
            'title' => 'Dons - Saisir',
            'headerTitle' => 'Dons',
            'pageTitle' => 'Saisir un don',
            'articles' => $articles,
            'errors' => [],
            'old' => [],
        ], 'content');

        $this->app->render('layouts/base');
    }

    public function createSubmit(): void
    {
        $idArticle = (int) ($this->app->request()->data->id_article ?? 0);
        $quantiteRaw = $this->app->request()->data->quantite_totale ?? null;
        $donateur = (string) ($this->app->request()->data->donateur ?? '');
        $source = (string) ($this->app->request()->data->source ?? '');
        $observations = (string) ($this->app->request()->data->observations ?? '');

        $old = [
            'id_article' => $idArticle,
            'quantite_totale' => $quantiteRaw,
            'donateur' => $donateur,
            'source' => $source,
            'observations' => $observations,
        ];

        $errors = [];

        $quantite = is_numeric($quantiteRaw) ? (float) $quantiteRaw : 0.0;

        if ($idArticle <= 0) {
            $errors[] = 'Veuillez choisir un article.';
        }
        if ($quantite <= 0) {
            $errors[] = 'La quantité totale doit être supérieure à 0.';
        }

        if ($errors !== []) {
            $articles = $this->articleModel->getAllWithUnite();

            $this->app->render('pages/dons/form', [
                'title' => 'Dons - Saisir',
                'headerTitle' => 'Dons',
                'pageTitle' => 'Saisir un don',
                'articles' => $articles,
                'errors' => $errors,
                'old' => $old,
            ], 'content');

            $this->app->render('layouts/base');
            return;
        }

        $donateurDb = trim($donateur);
        $sourceDb = trim($source);
        $obsDb = trim($observations);

        $this->donModel->create(
            $idArticle,
            $quantite,
            $donateurDb !== '' ? $donateurDb : null,
            $sourceDb !== '' ? $sourceDb : null,
            $obsDb !== '' ? $obsDb : null
        );

        $this->app->redirect('/dons');
    }

    public function vendreSubmit(): void
    {
        $idDon = (int) ($this->app->request()->data->id_don ?? 0);
        $quantiteRaw = $this->app->request()->data->quantite ?? null;
        $reductionRaw = $this->app->request()->data->reduction_percent ?? null;

        $quantite = is_numeric($quantiteRaw) ? (float) $quantiteRaw : 0.0;
        $reduction = is_numeric($reductionRaw) ? (float) $reductionRaw : 0.0;

        try {
            if ($idDon <= 0) {
                throw new \Exception('Don introuvable.');
            }
            if ($quantite <= 0) {
                throw new \Exception('Quantité invalide.');
            }

            $idDonArgent = $this->donModel->vendreMaterielEnArgent($idDon, $quantite, $reduction);

            $this->app->json([
                'success' => true,
                'id_don_argent' => $idDonArgent,
            ]);
        } catch (\Exception $e) {
            $this->app->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
