<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\AttributionModel;
use Exception;
use flight\Engine;

class AttributionController
{
    protected Engine $app;
    protected AttributionModel $attributionModel;

    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->attributionModel = new AttributionModel($app);
    }

    public function index(): void
    {
        $besoins = $this->attributionModel->listBesoinsWithCoverage();

        $this->app->render('pages/attributions/index', [
            'title' => 'Attribution',
            'headerTitle' => 'Attribution',
            'pageTitle' => 'Attribution des dons',
            'besoins' => $besoins,
        ], 'content');

        $this->app->render('layouts/base');
    }

    public function besoinDons(int $idBesoin): void
    {
        try {
            $besoin = $this->attributionModel->getBesoinDetails($idBesoin);
            $dons = $this->attributionModel->listDonsDisponiblesByArticle((int) $besoin['id_article']);

            $prixUnitaire = $this->attributionModel->getPrixUnitaireByArticle((int) $besoin['id_article']);
            $donsArgent = $this->attributionModel->listDonsArgentDisponibles();

            $totalArgentDisponible = 0.0;
            foreach ($donsArgent as $da) {
                $totalArgentDisponible += (float) $da->montant_disponible;
            }

            $totalDisponible = 0.0;
            foreach ($dons as $d) {
                $totalDisponible += (float) $d->quantite_disponible;
            }

            $this->app->json([
                'success' => true,
                'besoin' => $besoin,
                'total_disponible' => (float) $totalDisponible,
                'prix_unitaire' => $prixUnitaire !== null ? (float) $prixUnitaire : null,
                'total_argent_disponible' => (float) $totalArgentDisponible,
                'dons' => array_map(function($d) {
                    return [
                        'id_don' => (int) $d->id_don,
                        'quantite_totale' => (float) $d->quantite_totale,
                        'quantite_distribuee' => (float) $d->quantite_distribuee,
                        'quantite_disponible' => (float) $d->quantite_disponible,
                        'date_reception' => (string) $d->date_reception,
                        'donateur' => (string) ($d->donateur ?? ''),
                        'source' => (string) ($d->source ?? ''),
                    ];
                }, $dons),
            ]);
        } catch (Exception $e) {
            $this->app->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function createSubmit(): void
    {
        $idBesoin = (int) ($this->app->request()->data->id_besoin ?? 0);
        $mode = (string) ($this->app->request()->data->mode ?? 'stock');
        $quantiteRaw = $this->app->request()->data->quantite_attribuee ?? null;
        $quantite = is_numeric($quantiteRaw) ? (float) $quantiteRaw : 0.0;

        try {
            if ($idBesoin <= 0) {
                throw new Exception('Veuillez sélectionner un besoin.');
            }
            if ($quantite <= 0) {
                throw new Exception('La quantité attribuée doit être supérieure à 0.');
            }

            if ($mode === 'argent') {
                $this->attributionModel->createAttributionFromArgentFifo($idBesoin, $quantite);
            } else {
                $this->attributionModel->createAttributionFifo($idBesoin, $quantite);
            }

            $this->app->json([ 'success' => true ]);
        } catch (Exception $e) {
            $this->app->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
