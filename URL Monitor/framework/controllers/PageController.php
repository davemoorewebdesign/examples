<?php 
require_once('Controller.php');

class PageController extends Controller {
	
	// Show list of pages
	public function index() {
		$this->title = 'Home';
		$this->pageData['pages'] = Page::getAll();
		$this->render('index');
	}
	
	// Show page for specific traffic lights
	public function view() {
		// Find page by url slug
		$slug = $this->app->getSlug();
		$page = Page::findBySlug($slug);
		
		// If page exists render it, otherwise 404
		if ($page) {
			$this->title = $page->name;
			$this->pageData['page'] = $page;
			$this->render('view');
		} else {
			$this->noRoute();
		}
	}
	
}