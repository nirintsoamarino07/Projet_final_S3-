<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\ArticleModel;
use app\models\BesoinModel;
use app\models\RegionModel;
use app\models\VilleModel;
use flight\Engine;

class BesoinController
{
    protected Engine $app;
    protected BesoinModel $besoinModel;
    protected VilleModel $villeModel;
    protected ArticleModel $articleModel;
    protected RegionModel $regionModel;

    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->besoinModel = new BesoinModel($app);
        $this->villeModel = new VilleModel($app);
        $this->articleModel = new ArticleModel($app);
		$this->regionModel = new RegionModel($app);
    }

    public function index(): void
    {
        $besoins = $this->besoinModel->listAll();

        $this->app->render('pages/besoins/list', [
            'title' => 'Besoins - Liste',
            'headerTitle' => 'Besoins',
            'pageTitle' => 'Liste des besoins',
            'besoins' => $besoins,
        ], 'content');

        $this->app->render('layouts/base');
    }

    public function createForm(): void
    {
        $villes = $this->villeModel->getAll();
        $articles = $this->articleModel->getAllWithUnite();
		$regions = $this->regionModel->getAll();

        $this->app->render('pages/besoins/form', [
            'title' => 'Besoins - Saisir',
            'headerTitle' => 'Besoins',
            'pageTitle' => 'Saisir un besoin',
            'villes' => $villes,
			'regions' => $regions,
            'articles' => $articles,
            'errors' => [],
            'old' => [],
        ], 'content');

        $this->app->render('layouts/base');
    }

    public function createSubmit(): void
    {
        $idVille = (int) ($this->app->request()->data->id_ville ?? 0);
        $idArticle = (int) ($this->app->request()->data->id_article ?? 0);
        $quantiteRaw = $this->app->request()->data->quantite_demandee ?? null;
        $observations = (string) ($this->app->request()->data->observations ?? '');

        $old = [
            'id_ville' => $idVille,
            'id_article' => $idArticle,
            'quantite_demandee' => $quantiteRaw,
            'observations' => $observations,
        ];

        $errors = [];

        $quantite = is_numeric($quantiteRaw) ? (float) $quantiteRaw : 0.0;

        if ($idVille <= 0) {
            $errors[] = 'Veuillez choisir une ville.';
        }
        if ($idArticle <= 0) {
            $errors[] = 'Veuillez choisir un article.';
        }
        if ($quantite <= 0) {
            $errors[] = 'La quantité demandée doit être supérieure à 0.';
        }

        if ($errors !== []) {
            $villes = $this->villeModel->getAll();
            $articles = $this->articleModel->getAllWithUnite();
			$regions = $this->regionModel->getAll();

            $this->app->render('pages/besoins/form', [
                'title' => 'Besoins - Saisir',
                'headerTitle' => 'Besoins',
                'pageTitle' => 'Saisir un besoin',
                'villes' => $villes,
				'regions' => $regions,
                'articles' => $articles,
                'errors' => $errors,
                'old' => $old,
            ], 'content');

            $this->app->render('layouts/base');
            return;
        }

        $obs = trim($observations);
        $this->besoinModel->create($idVille, $idArticle, $quantite, $obs !== '' ? $obs : null);

        $this->app->redirect('/besoins');
    }

	public function createVille(): void
	{
		$nomVille = trim((string) ($this->app->request()->data->nom_ville ?? ''));
		$idRegion = (int) ($this->app->request()->data->id_region ?? 0);

		if ($nomVille === '' || $idRegion <= 0) {
			$this->app->json([
				'success' => false,
				'message' => 'Veuillez renseigner la région et le nom de la ville.',
			], 400);
			return;
		}

		$idVille = $this->villeModel->create($nomVille, $idRegion);
		$this->app->json([
			'success' => true,
			'ville' => [
				'id_ville' => $idVille,
				'nom_ville' => $nomVille,
			],
		]);
	}
}
