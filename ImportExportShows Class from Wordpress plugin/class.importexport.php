<?php
class ImportExportShows {
	
	public static function importData($entryData, $post_type) {
		$type_name = $post_type=='ffvenue'?'Venue':'Show';
		echo "Importing {$type_name}s...<br/>";
		
		$fields = array();
		$keys = array();
		$types = array();
		
		$fileName = $_FILES["file"]["tmp_name"];
		if ($_FILES["file"]["size"] > 0) {
			$file = fopen($fileName, "r");
			while (($row = fgetcsv($file, 10000, ",")) !== FALSE) {
				if ($i==0) {
					$fieldNames = $row;
					foreach($fieldNames as $name) {
						if (strpos($name, 'acf_value_') !== false) {
							$fields[str_replace('acf_value_', '', $name)] = 'acf_value';
						} else if (strpos($name, 'acf_object_') !== false) {
							$fields[str_replace('acf_object_', '', $name)] = 'acf_object';
						} else {
							$fields[$name] = 'value';
						}
					}
					$keys = array_keys($fields);
					$types = array_values($fields);
				} else {
					$data = $row;
					if (count($data)) {
						
						
						$ID = $data[0];
						echo "<br/>Row ".$i." - ID = ".($ID?$ID:'NEW')."<br/>";
						$title = $data[1];
						$post = null;
						if (is_numeric($ID)) {
							$post = get_post($ID);
						}
						if ($post && $title) {
							$postData = array(
								'ID' => $ID,
								'post_title' => $title,
							);
							wp_update_post($postData);
						} else if ($title) {
							$ID = wp_insert_post(array(
								'post_type' => $post_type,
								'post_title' => $title, 
								'post_status' => 'publish'
							));
							echo "New ".$type_name." created: ".$ID."<br/>";
							$post = get_post($ID);
						}
						if (ID && $post) {
							for ($n = 2; $n<count($types); $n++) {
								$key = $keys[$n];
								$type = $types[$n];
								$value = $data[$n];
								if ($key == "show_no") {
									$show_no = $value;
								}
								if (!$value) {
									$value = "";
								}
								switch($type) {
									case "value":
										break;
									case "acf_value":
										echo "Updating '".$key."' : ".$value."<br/>";
										if ($post_type == 'ffshow' && $key == "photo") {
											if (strpos($value, '.') !== false || ctype_alpha($value)) {
												$newID = self::get_image_id_by_slug($value);
												echo "- Photo search for '".$value."' found ID: ".$newID."<br/>";
												$value = $newID;
											} else if ($value == "" && $show_no) {
												$slug = sprintf("%04d", intval($show_no));
												echo "Slug = ".$slug."<br/>";
												$newID = self::get_image_id_by_slug($slug);
												echo "- Photo search for '".$value."' found ID: ".$newID."<br/>";
												$value = $newID;
											}
										}
										if ($key == 'dates' && trim($value) != '-' && trim($value) != '') {
											$value = str_replace(array('mon','tue','wed','thu','fri','sat','sun',' '),'', strtolower($value));
											if (strpos($value, ',') === false) {
												if (substr_count($value, "-") == 1) {
													$valueParts = explode('-', $value);
													if (count($valueParts) == 2 && $valueParts[0] == $valueParts[1]) {
														$value = $valueParts[0];
													}
												}
											}
											$dateParts = explode(',', $value);
											$dates = array();
											foreach($dateParts as $part) {
												$trimmedPart = trim($part);
												if (strpos($trimmedPart, '-') !== false) {
													$rangeParts = explode('-', $trimmedPart);
													if (count($rangeParts) == 2 && is_numeric($rangeParts[0]) && is_numeric($rangeParts[1])) {
														$newDates = range($rangeParts[0], $rangeParts[1]);
														$dates = array_merge($dates, $newDates);
													}
												} else if (is_numeric($trimmedPart)) {
													$dates[] = $trimmedPart;
												}
												if (count($dates)) {
													update_field('dates_read_only', ','.implode(',', $dates).',', $ID);
												} else {
													update_field('dates_read_only', '', $ID);
												}
											}
										}
										update_field($key, $value, $ID);
										break;
									case "acf_object":
										$title = str_replace('&#8211;', '-', $value);
										$object = get_page_by_title($title, "OBJECT", 'ff'.$key);
										echo "Updating '".$key."' : ".$title."<br/>";
										if ($object) {
											update_field($key, $object->ID, $ID);
										}
										break;
								}
							}
						}
					}
				}
				$i++;
			}
		}
	}
	
	public static function get_image_id_by_slug($slug) {
		if (strpos($slug, '.') !== false) {
			$slugParts = explode('.', $slug);
			$slug = $slugParts[0];
		}
		$args = array(
			'post_type' => 'attachment',
			'name' => sanitize_title($slug),
			'posts_per_page' => 1,
			'post_status' => 'inherit',
		);
		$images = get_posts( $args );
		$image = $images?array_pop($images):null;
		if ($image) {
			return $image->ID;
		} else {
			return null;
		}
	}
	
	public static function get_show_fields() {
		return array(
			'ID'=>'value',
			'post_title'=>'value',
			'show_no'=>'acf_value',
			'venue'=>'acf_object',
			'dates'=>'acf_value',
			'time'=>'acf_value',
			'duration'=>'acf_value',
			'genre'=>'acf_object',
			'blurb'=>'acf_value',
			'year'=>'acf_value',
			'photo'=>'acf_value',
			'website'=>'acf_value',
			'suitability'=>'acf_value',
		);
	}
	
	public static function get_venue_fields() {
		return array(
			'ID'=>'value',
			'post_title'=>'value',
			'venue_no'=>'acf_value',
			'address'=>'acf_value',
			'photo'=>'acf_value',
			'phone'=>'acf_value',
			'opening_times'=>'acf_value',
			'age_restriction'=>'acf_value',
			'accessibility'=>'acf_value',
			'lat'=>'acf_value',
			'lng'=>'acf_value',
		);
	}
	
	public static function generate_show_csv($year) {
		
		$fields = self::get_show_fields();
		
		return self::generate_csv('ffshow', $fields, $year);
	}
	
	public static function generate_venue_csv() {
		
		$fields = self::get_venue_fields();
		
		return self::generate_csv('ffvenue', $fields, null);
	}
	
	public static function generate_csv($post_type, $fields, $year) {
		$csv_output = '';
		
		foreach($fields as $name => $type) {
			if ($type == "value") {
				$colname = $name;
			} else {
				$colname = $type.'_'.$name;
			}
			$csv_output = $csv_output . $colname . "|";
		}
		$csv_output .= "\n";
		
		if ($year) {
			// If current year matches show year
			$meta_query = array(
				array(
					'key' => 'year',
					'value' => $year,
					'compare' => '=',
				)
			);
		}
		
		// Add rows
		$args = array(
			'post_type' => array($post_type),
			'post_status' => array('publish'),
			'posts_per_page' => -1,
			'order' => 'ASC',
			'orderby' => 'title',
			'meta_query' => $meta_query,
		);
		
		$query = new WP_Query($args);
		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				foreach($fields as $name => $type) {
					switch($type) {
						case "value":
							switch ($name) {
								case 'post_title':
									$value = get_the_title();
									break;
								case 'ID':
									$value = get_the_ID();
									break;
							}
							break;
						case "acf_value":
							$value = get_field($name);
							break;
						case "acf_object":
							$object = get_field($name);
							$value = get_the_title($object);
							break;
						default:
							$value = '';
					}
					$value = str_replace(array('\n', '\r', '\r\n', '\n\r'), '', $value);
					$csv_output .= '"'.$value.'"|';
				}
				$csv_output .= "\n";
			}
		}

		return $csv_output;
	}

}