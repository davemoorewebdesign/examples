<?php
// Change database details and base url in this file if necessary
require_once('config.php');

// Required models
require_once('framework/models/Model.php');
require_once('framework/models/DB.php');
require_once('framework/models/Page.php');
require_once('framework/models/Trafficlight.php');

class App {
	private $_slug;
	private $_testCode;
	private $_route = array();
	private $_baseUrl;
	private $_siteTitle;
	
    public function __construct() {	
		$this->_baseUrl = BASE_URL;
		$this->_siteTitle = SITE_TITLE;
		
		// Set page slug from query string and sanitise
		$this->_slug = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);
		$this->_testCode = filter_input(INPUT_GET, 'code', FILTER_SANITIZE_NUMBER_INT);
		
		// Set route using slug
		$this->_route = $this->getRoutePath($this->_slug);
		
		// Set route names ready for calling
		$controllerName = $this->_route['controller'].'Controller';
		$action = $this->_route['action'];
		
		// Required controller
		require_once('framework/controllers/'.$controllerName.'.php');
		
		// Initialise controller and run action
		$controller = new $controllerName($this);
		$controller->$action();
		
		// Close DB
		self::getDB()->close();
	}
	
	// Decide route based on slug or if testCode is set
	private function getRoutePath($slug) {
		
		$route = array();
		if ($this->_testCode) {
			$route['controller'] = 'Status';
			$route['action'] = 'test';
		} else {
			switch ($slug) {
				// If no slug, set route to for page index (homepage)
				case '' :
					$route['controller'] = 'Page';
					$route['action'] = 'index';
					break;
				// If status page, set route to Status index
				case 'status' :
					$route['controller'] = 'Status';
					$route['action'] = 'fetch';
					break;
				
				// If a page, then check it has a slug and if not, 404
				default:
					$route['controller'] = 'Page';
					$route['action'] = 'view';
					break;
			}
		}
		
		return $route;
	}
	
	// Make slug read only
	public function getSlug() {
		return $this->_slug;
	}
	
	// Make route read only
	public function getRoute() {		
		return $this->_route;
	}
	
	// Make baseUrl read only
	public function getBaseUrl() {
		return $this->_baseUrl;
	}
	
	// Make siteTitle read only
	public function getSiteTitle() {
		return $this->_siteTitle;
	}
	
	// Make testCode read only
	public function getTestCode() {
		return $this->_testCode;
	}
	
	// Allow db to be accessed via static method so no "global" needed
	public static function getDB() {
		global $db;
		
		// Only connect if a connection doesn't already exist
		if (!$db) {
			$db = new DB(DBHOST, DBUSER, DBPASS, DBNAME);
		}
		return $db;
		
	}
	
}