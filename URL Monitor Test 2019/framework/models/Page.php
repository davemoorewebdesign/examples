<?php
class Page extends Model {
	
    public $id;
	public $name;
	public $slug;
	
	public function __construct($id, $name = '', $slug = '') {
		$this->id = $id;
		$this->name = $name;
		$this->slug = $slug;
	}
	
	// Find a page in the db by it's url slug
	public static function findBySlug($slug) {
		$result = App::getDB()->query("SELECT * FROM page WHERE slug = \"{$slug}\" ORDER BY name;");
		$row = $result->fetch_assoc();
		if ($row) {
			$page = new Page($row['page_id'], $row['name'], $row['slug']);
			return $page;
		}
		
		return false;
	}
	
	// Get all pages from db
	public static function getAll() {
		$pages = array();
		
		$result = App::getDB()->query('SELECT * FROM page ORDER BY name;');
		while($row = mysqli_fetch_assoc($result)){
			$page = new Page($row['page_id'], $row['name'], $row['slug']);
			$pages[] = $page;
		}
		
		return $pages;
	}
	
	// Get all traffic lights for this page from the db and return populated objects
	public function getTrafficlights() {
		if (!$this->id) {
			return array();
		}
		
		$trafficlights = array();
		
		$result = App::getDB()->query("SELECT * FROM page_trafficlight pt INNER JOIN trafficlight t ON pt.trafficlight_id = t.trafficlight_id WHERE pt.page_id = \"{$this->id}\" ORDER BY t.name;");
		while($row = mysqli_fetch_assoc($result)){
			$trafficlight = new Trafficlight($row['trafficlight_id'], $row['name'], $row['url'], $row['frequency']);
			$trafficlights[] = $trafficlight;
		}
		
		return $trafficlights;
	}
}