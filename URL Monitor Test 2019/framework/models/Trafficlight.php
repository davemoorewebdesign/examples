<?php
class Trafficlight extends Model {
	
	public $id;
	public $name;
	public $url;
	public $frequency;
	
	public function __construct($id, $name = '', $url = '', $frequency = 10) {
		$this->id = $id;
		$this->name = $name;
		$this->url = $url;
		$this->frequency = $frequency;
	}
}