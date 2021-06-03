<?php 
require_once('Controller.php');

class StatusController extends Controller {
	
	// Get the http code from a url
	public function fetch() {
		$url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_STRING);
		
		// If part of same website
		if (substr($url, 0, 4) != 'http') {
			$url = $this->app->getBaseUrl().'/'.$url;
		}
		
		// Setup curl for quick response that can be captured
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		
		// Request url
		$response = curl_exec($ch);
		
		// Get http code from response
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		echo $httpCode;
	}
	
	// Get the http code from a url
	public function test() {
		// Sanitise $_GET['code']
		$httpCode = $this->app->getTestCode();
		
		// Send header with http code
		http_response_code($httpCode);
	}
	
}