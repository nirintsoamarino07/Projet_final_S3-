<?php

namespace app\controllers;

use flight\Engine;
use app\models\ListCourseModele;
use Flight ;

class ApiExampleController {

	protected Engine $app;

	public function __construct($app) {
		$this->app = $app;
	}

	public function getListCourse() {
			$listCourse = new Modele(Flight::db());
			$course = $listCourse->getListCourse();
			Flight::render('list_course', ['course' => $course]);
	}
}