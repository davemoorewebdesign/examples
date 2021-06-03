<?php
class Controller {
	
	public $app;
	public $layout = 'main';
	public $siteTitle = 'Url Monitor';
	public $title = '';
	public $content;
	public $pageData;
	
	public function __construct($app) {
		$this->app = $app;
	}
	
	// Index page renderer
	public function index() {
		$this->title = 'Index';
		$this->render('index');
	}
	
	// View page renderer
	public function view() {
		$this->title = 'View';
		$this->render('view');
	}
	
	// 404 page renderer
	public function noRoute() {
		http_response_code(404);
		$this->title = 'Page not found.';
		$this->render('/404');
	}
	
	// Return site title + page title
	public function getMetaTitle() {
		return $this->app->getSiteTitle().' - '.$this->title;
	}
	
	// Render page content and wrap it in layout
	protected function render($template) {
		$route = $this->app->getRoute();
		
		// Check if relative or absolute template
		$suffix = substr($template, 0, 1) != '/'?'/'.$route['controller'].'/':'';
		
		// store content in variable
		ob_start();
		require_once('framework/views'.$suffix.$template.'.php');
		$this->content = ob_get_contents();
		ob_end_clean();
		
		// Embed content in layout if set, otherwise show content
		if ($this->layout) {
			require_once('framework/views/'.$this->layout.'.php');
		} else {
			echo $this->content;
		}
	}
	
}