<?php

use app\controllers\ApiExampleController;
use app\controllers\BesoinController;
use app\controllers\DonController;
use app\controllers\AttributionController;
use app\controllers\DashboardController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function(Router $router) use ($app) {

	$router->get('/', [ DashboardController::class, 'index' ]);

	$router->get('/hello-world/a/@name', function($name) {
		echo '<h1>Hello world! Oh hey '.$name.'!</h1>';
	});

	$router->group('/besoins', function() use ($router) {
		$router->get('', [ BesoinController::class, 'index' ]);
		$router->get('/', [ BesoinController::class, 'index' ]);
		$router->get('/saisir', [ BesoinController::class, 'createForm' ]);
		$router->post('/saisir', [ BesoinController::class, 'createSubmit' ]);
		$router->post('/villes', [ BesoinController::class, 'createVille' ]);
	});

	$router->group('/dons', function() use ($router) {
		$router->get('', [ DonController::class, 'index' ]);
		$router->get('/', [ DonController::class, 'index' ]);
		$router->get('/saisir', [ DonController::class, 'createForm' ]);
		$router->post('/saisir', [ DonController::class, 'createSubmit' ]);
	});

	$router->group('/attributions', function() use ($router) {
		$router->get('', [ AttributionController::class, 'index' ]);
		$router->get('/', [ AttributionController::class, 'index' ]);
		$router->get('/besoin/@id:[0-9]+/dons', [ AttributionController::class, 'besoinDons' ]);
		$router->post('', [ AttributionController::class, 'createSubmit' ]);
		$router->post('/', [ AttributionController::class, 'createSubmit' ]);
	});

	$router->group('/api', function() use ($router) {
		$router->get('/users', [ ApiExampleController::class, 'getUsers' ]);
		$router->get('/users/@id:[0-9]', [ ApiExampleController::class, 'getUser' ]);
		$router->post('/users/@id:[0-9]', [ ApiExampleController::class, 'updateUser' ]);
	});

	$router->group('/dashboard', function() use ($router) {
		$router->get('/besoin/@id:[0-9]+/attributions', [ DashboardController::class, 'attributions' ]);
	});
	
}, [ SecurityHeadersMiddleware::class ]);
