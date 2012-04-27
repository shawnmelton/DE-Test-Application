<?php
/*!
 * @desc 
 */
class Controller {
	/*!
	 * @desc 404 page
	 */
	public function notFound() {
		header('HTTP/1.0 404 Not Found');
		$view = new View();
		$view->content = $view->render('404.phtml');
		
		$view->title = 'Page Not Found';
		echo $view->render('layout.phtml');
	}
	
	
	/*!
	 * @desc Home page
	 */
	public function index() {
		$view = new View();
		$view->content = $view->render('home.phtml');
		
		$view->title = 'Home';
		echo $view->render('layout.phtml');
	}
}