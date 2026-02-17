<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\ArticleModel;
use app\models\PrixUnitaireModel;
use Exception;
use flight\Engine;

class PrixUnitaireController
{
    protected Engine $app;
    protected PrixUnitaireModel $prixUnitaireModel;
    protected ArticleModel $articleModel;

    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->prixUnitaireModel = new PrixUnitaireModel($app);
        $this->articleModel = new ArticleModel($app);
    }

    public function index(): void
    {
        $prixUnitaires = $this->prixUnitaireModel->listAll();

        $this->app->render('pages/prix_unitaires/list', [
            'title' => 'Prix unitaires - Liste',
            'headerTitle' => 'Prix unitaires',
            'pageTitle' => 'Liste des prix unitaires',
            'prixUnitaires' => $prixUnitaires,
        ], 'content');

        $this->app->render('layouts/base');
    }

    public function createForm(): void
    {
        $articles = $this->articleModel->getAllWithUnitePrixUnitaire();

        $this->app->render('pages/prix_unitaires/form', [
            'title' => 'Prix unitaires - Saisir',
            'headerTitle' => 'Prix unitaires',
            'pageTitle' => 'Saisir un prix unitaire',
            'articles' => $articles,
            'errors' => [],
            'old' => [],
            'isEdit' => false,
        ], 'content');

        $this->app->render('layouts/base');
    }

    public function createSubmit(): void
    {
        $idArticle = (int) ($this->app->request()->data->id_article ?? 0);
        $prixRaw = $this->app->request()->data->prix ?? null;
        $prix = is_numeric($prixRaw) ? (float) $prixRaw : -1.0;

        $old = [
            'id_article' => $idArticle,
            'prix' => $prixRaw,
        ];

        $errors = [];
        if ($idArticle <= 0) {
            $errors[] = 'Veuillez choisir un article.';
        }
        if (!is_numeric($prixRaw) || $prix < 0) {
            $errors[] = 'Veuillez saisir un prix valide (>= 0).';
        }

        if ($errors !== []) {
            $articles = $this->articleModel->getAllWithUnitePrixUnitaire();
            $this->app->render('pages/prix_unitaires/form', [
                'title' => 'Prix unitaires - Saisir',
                'headerTitle' => 'Prix unitaires',
                'pageTitle' => 'Saisir un prix unitaire',
                'articles' => $articles,
                'errors' => $errors,
                'old' => $old,
                'isEdit' => false,
            ], 'content');
            $this->app->render('layouts/base');
            return;
        }

        try {
            $this->prixUnitaireModel->create($idArticle, $prix);
            $this->app->redirect('/prix-unitaires');
        } catch (Exception $e) {
            $articles = $this->articleModel->getAllWithUnitePrixUnitaire();
            $this->app->render('pages/prix_unitaires/form', [
                'title' => 'Prix unitaires - Saisir',
                'headerTitle' => 'Prix unitaires',
                'pageTitle' => 'Saisir un prix unitaire',
                'articles' => $articles,
                'errors' => [ $e->getMessage() ],
                'old' => $old,
                'isEdit' => false,
            ], 'content');
            $this->app->render('layouts/base');
        }
    }

    public function editForm(int $id): void
    {
        $prixUnitaire = $this->prixUnitaireModel->getById($id);
        $articles = $this->articleModel->getAllWithUnitePrixUnitaire();

        $this->app->render('pages/prix_unitaires/form', [
            'title' => 'Prix unitaires - Modifier',
            'headerTitle' => 'Prix unitaires',
            'pageTitle' => 'Modifier un prix unitaire',
            'articles' => $articles,
            'errors' => [],
            'old' => [
                'id_article' => $prixUnitaire['id_article'],
                'prix' => $prixUnitaire['prix'],
            ],
            'isEdit' => true,
            'idPrixUnitaire' => $id,
        ], 'content');

        $this->app->render('layouts/base');
    }

    public function editSubmit(int $id): void
    {
        $idArticle = (int) ($this->app->request()->data->id_article ?? 0);
        $prixRaw = $this->app->request()->data->prix ?? null;
        $prix = is_numeric($prixRaw) ? (float) $prixRaw : -1.0;

        $old = [
            'id_article' => $idArticle,
            'prix' => $prixRaw,
        ];

        $errors = [];
        if ($idArticle <= 0) {
            $errors[] = 'Veuillez choisir un article.';
        }
        if (!is_numeric($prixRaw) || $prix < 0) {
            $errors[] = 'Veuillez saisir un prix valide (>= 0).';
        }

        if ($errors !== []) {
            $articles = $this->articleModel->getAllWithUnitePrixUnitaire();
            $this->app->render('pages/prix_unitaires/form', [
                'title' => 'Prix unitaires - Modifier',
                'headerTitle' => 'Prix unitaires',
                'pageTitle' => 'Modifier un prix unitaire',
                'articles' => $articles,
                'errors' => $errors,
                'old' => $old,
                'isEdit' => true,
                'idPrixUnitaire' => $id,
            ], 'content');
            $this->app->render('layouts/base');
            return;
        }

        try {
            $this->prixUnitaireModel->update($id, $idArticle, $prix);
            $this->app->redirect('/prix-unitaires');
        } catch (Exception $e) {
            $articles = $this->articleModel->getAllWithUnitePrixUnitaire();
            $this->app->render('pages/prix_unitaires/form', [
                'title' => 'Prix unitaires - Modifier',
                'headerTitle' => 'Prix unitaires',
                'pageTitle' => 'Modifier un prix unitaire',
                'articles' => $articles,
                'errors' => [ $e->getMessage() ],
                'old' => $old,
                'isEdit' => true,
                'idPrixUnitaire' => $id,
            ], 'content');
            $this->app->render('layouts/base');
        }
    }

    public function deleteSubmit(int $id): void
    {
        try {
            $this->prixUnitaireModel->delete($id);
            $this->app->redirect('/prix-unitaires');
        } catch (Exception $e) {
            $this->app->redirect('/prix-unitaires');
        }
    }
}
