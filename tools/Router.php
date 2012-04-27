<?php
/*!
 * @desc Route traffic of site.
 * @author Shawn Melton <shawn.a.melton@gmail.com>
 * @version $id: $
 */
class Router {
	static function run() {
		$bits = explode('/', str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']));
		$siteroot = dirname(dirname(__FILE__));
		
		// Default to home page.
		$controller = 'Controller';
		$action = 'index';
		
		// Determine controller
		if( isset($bits[1]) && ($bits[1] = strtolower(preg_replace('/\W/', '', $bits[1]))) != '' ) {
			$controller = ucwords(strtolower($bits[1])) .'Controller';
			if( !file_exists($siteroot .'/controllers/'. $controller .'.php') ) {
				$controller = 'Controller';
				$action = 'notFound';
			}
		}
		
		// Instantiate controller
		$controller = new $controller();
		
		// Determine method
		if( $action != 'notFound' && isset($bits[2]) && ($bits[2] = strtolower(preg_replace('/\W/', '', $bits[2]))) != '' ) {
			$action = strtolower($bits[2]);
			if( !method_exists($controller, $action) ) {
				$controller = new Controller();
				$action = 'notFound';
			}
		}
		
		$controller->$action();
	}
}