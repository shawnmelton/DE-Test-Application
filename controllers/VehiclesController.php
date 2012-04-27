<?php
/*!
 * @desc Vehicles Controller
 */
class VehiclesController {
	public function add() {
		$vehicle = new Vehicles();
		if( $vehicle->hasSubmission() ) {
			$vehicle->process();
			
			header('Location: /vehicles');
			exit;
		}
	
		$view = new View();
		$view->content = $view->render('vehicles/add.phtml');
		echo $view->render('layout.phtml');
	}
	
	
	public function edit() {
		$vehicle = new Vehicles();
		
		if( !isset($_GET['id']) || !is_numeric($_GET['id']) || $_GET['id'] < 1 ) {
			header('Location: /vehicles');
			exit;
		}
		
		if( $vehicle->hasSubmission() ) {
			$vehicle->process($_GET['id']);
			
			header('Location: /vehicles');
			exit;
		}
	
		$view = new View();
		$view->info = $vehicle->load($_GET['id']);
		$view->content = $view->render('vehicles/edit.phtml');
		echo $view->render('layout.phtml');
	}


	/*!
	 * @desc List vehicles.
	 */
	public function index() {
		$vehicles = new Vehicles();
		
		$view = new View();
		$view->vehicles = $vehicles->get();
		$view->content = $view->render('vehicles/list.phtml');
		echo $view->render('layout.phtml');
	}
}