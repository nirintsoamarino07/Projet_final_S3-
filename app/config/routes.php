<?php

use app\controllers\ApiExampleController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function(Router $router) use ($app) {

	// $router->get('/', function() use ($app) {
	// 	$app->render('welcome', [ 'message' => 'You are gonna do great things!']);
	// });

	// $router->get('/elias', function() use ($app) {
	// 	$app->render('welcome', [ 'message' => 'You are gonna do great thsdfsdfsdf!']);
	// });

	// $router->get('/hello-world/a/@name', function($name) {
	// 	echo '<h1>Hello world! Oh hey '.$name.'!</h1>';
	// });
	
	$router->group('/taximotos', function() use ($router) {
    $router->get('/total', [ ApiExampleController::class, 'total' ]);
    $router->get('/listCourse', [ ApiExampleController::class, 'getListCourse' ]);
    $router->get('/creation_course', [ ApiExampleController::class, 'creationCourse' ]);
    $router->post('/creation_course', [ ApiExampleController::class, 'creationCoursePOST' ]);
    $router->get('/modifier_course', [ ApiExampleController::class, 'modifierCourse' ]);
    $router->post('/modifier_course', [ ApiExampleController::class, 'modifierCoursePOST' ]);
 	$router->post('/valider_course', [ ApiExampleController::class, 'validerCoursePOST' ]);
 	$router->get('/Modife_prix_essence', [ ApiExampleController::class, 'ModifePrix' ]);
 	$router->post('/Modife_prix_essence', [ ApiExampleController::class, 'ModifePrixPOST' ]);
 	$router->post('/supprimer_courses', [ ApiExampleController::class, 'supprCourses' ]);
 	$router->post('/filtre_date', [ ApiExampleController::class, 'filtrerDate' ]);

});
 
}, [ SecurityHeadersMiddleware::class ]);  