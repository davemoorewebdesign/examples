<?php
class Geocode {
		
	public function my_acf_save_post($post_id) {
		remove_action('acf/save_post', 'my_acf_save_post');
		$post_type = get_post_type($post_id);
		if ($post_type === 'dmlocation') {
			$location = get_post($post_id);
			$lat_lng = self::getLatLng($location);
			if ($lat_lng) {
				$lat = $lat_lng['lat'];
				$lng = $lat_lng['lng'];
			}
			if ($location) {			
				update_field('lat', $lat, $post_id);
				update_field('lng', $lng, $post_id);
			}
		}
	}
	
	public static function getLatLng($venue){
		$address = get_the_title($venue).' '.get_field('address', $venue, false);
		$address = trim(preg_replace('/\s\s+/', ' ', $address));
		$lat_lng = self::geocode($address);
		return $lat_lng;
	}
	
	public static function geocode($address){
		$address = urlencode($address);
		
		$key = '********************************';
		$url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$key}";
			
		$resp_json = file_get_contents($url);
		$resp = json_decode($resp_json, true);
		
		if ($resp['status']=='OK'){	
			$lat = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
			$lng = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";
			$formatted_address = isset($resp['results'][0]['formatted_address']) ? $resp['results'][0]['formatted_address'] : "";
			
			if ($lat && $lng) {
				$lat_lng = array(
					'lat' => $lat,
					'lng' => $lng,
				);
				
				return $lat_lng;
			}
		}
		return false;
	}
}
?>