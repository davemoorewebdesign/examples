<?php
class GigTable {
	
	static function createTableHtml($table_data) {
		/* Required $tableDataFormat
		$tableData = array(
			'class' => '',
			'id' => '',
			'sections' => array(
				'thead' => array(
					'class' => '',
					'rows' => array(
						array(
							'class' => '',
							'cols' => array(
								array(
									'class' => '',
									'content' => '', 
								),
								array(
									'class' => '',
									'content' => '', 
								),
							),
						),
					}
				),
			),
		);
		*/
		
		if (!is_array($table_data) || !count($table_data['sections'])) {
			return false;
		}
		
		$data_values = explode(' ', $table_data['data']);
		$final_values = array();
		foreach($data_values as $data_value) {
			$parts = explode('-', $data_value);
			$final_values[] = 'data-'.$parts[0].'="'.$parts[1].'"';
		}
		$table_values = '';
		if (count($final_values)) {
			$table_values = ' '.implode(' ', $final_values);
		}
		$table_classes = explode(' ', $table_data['class']);
		if (count($table_classes)) {
			$container_classes = ' '.implode('-container ', $table_classes).'-container';
		}
		$table_class = $table_data['class']?' '.$table_data['class']:'';
		$table_id = $table_data['id']?' class="'.$table_data['id'].'"':'';
		ob_start();
		?>
		<div class="data-table-container<?php echo $container_classes; ?>">
			<table<?php echo $table_id.' class="data-table stripe order-column row-border'.$table_class.'"'; ?><?php echo $table_values; ?>>
				<?php
				foreach($table_data['sections'] as $section_key => $section) {
					$section_class = $section['class']?' class="'.$section['class'].'"':'';
					?>
					<<?php echo $section_key;?><?php echo $section_class; ?>>
						<?php
						$rows = $section['rows'];
						foreach($rows as $row) {
							$row_class = $row['class']?' class="'.$row['class'].'"':'';
							$cols = $row['cols'];
							if (count($cols)) {
								?>
				<tr<?php echo $row_class; ?>>
							<?php
							foreach($cols as $col) {
								$col_class = $col['class']?' class="'.$col['class'].'"':'';
								$col_class = $col['class']?' class="'.$col['class'].'"':'';
								if ($section_key == 'thead') {
									$col_tag = 'th';
								} else {
									$col_tag = 'td';
								}
								?>
					<<?php echo $col_tag.$col_class; ?>><?php echo $col['content']; ?></<?php echo $col_tag; ?>>
								<?php
							}
							?>
				</tr>
								<?php
							}
						}
						?>
			</<?php echo $section_key;?>>
					<?php
				}
				?>
			</table>
		</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	}
 
	function datatables_server_side_callback() {
		 
		header("Content-Type: application/json");
		
		$request = $_POST;
		
		$col_no = intval($request['order'][0]['column']);
		$search = sanitize_text_field($request['search']['value']);
		$order = sanitize_text_field($request['order'][0]['dir']);
		
		if ($request['filters']) {
			$filters = json_decode( str_replace("\\", "",$request['filters']));
		}
		$args = array();
		global $current_user;
		get_currentuserinfo();
		$filter_user_entries = intval(trim($filters->user_entries));
		if ($current_user && $filter_user_entries > 0) {
			if (!current_user_can('administrator')) {
				$args['author'] = $current_user->ID;
			}
			if ($filter_user_entries == 2) {
				$args['post_status'] = array('private');
			} else {
				$args['post_status'] = array('publish');
			}
		} else {
			$args['post_status'] = 'publish';
		}
		
		switch (sanitize_text_field($_GET['posttype'])) {
			case 'mgband':
					$columns = array(
						0 => 'title', 
						1 => 'genre',
						2 => 'country',
						3 => 'band_url'
					);
					if (in_array($col_no, array(0))) {
						$args['orderby'] = $columns[$col_no];
					} else if (in_array($col_no, array(2))) {
						$args['orderby'] = 'meta_value_num';
						$args['meta_key'] = $columns[$col_no];
					}
					if ($filter_band = trim(sanitize_text_field($filters->band))) {
						$args['search_post_title'] = $filter_band;
					}
					if ($filter_genre = trim(sanitize_text_field($filters->genre))) {
						$args['meta_query'][] = array(
							'key'		=> 'genre',
							'compare'	=> '=',
							'value'		=> $filter_genre,
						);
					}
					if ($filter_country = trim(sanitize_text_field($filters->country))) {
						$args['meta_query'][] = array(
							'key'		=> 'country',
							'compare'	=> 'LIKE',
							'value'		=> $filter_country,
						);
					}
					
					$edit_link = get_permalink(1748);
					$type = 'mgband';
					break;
			case 'mgevent':
					$columns = array( 
						0 => 'day_0_date', 
						1 => 'event_name',
						2 => 'bands',
						3 => 'genres',
						4 => 'venue',
						5 => 'town',
						6 => 'entry_url'
					);
					if (in_array($col_no, array(1))) {
						$args['orderby'] = $columns[$col_no];
					} else if (in_array($col_no, array(0, 2))) {
						$args['orderby'] = 'meta_value_num';
						$args['meta_key'] = $columns[0];
					}
					$args['meta_query'] = array(
						'relation' => 'AND',
					);
					if ($filter_date_from = trim(sanitize_text_field($filters->date_from))) {
						$args['meta_query'][] = array(
							'key'		=> 'day_$_date',
							'compare'	=> '>=',
							'value'		=> date('Ymd', strtotime(str_replace('/','-', $filter_date_from))),
						);
					}
					if ($filter_event_type = trim(sanitize_text_field($filters->event_type))) {
						$args['meta_query'][] = array(
							'key'		=> 'event_type',
							'compare'	=> '=',
							'value'		=> $filter_event_type,
						);
					}
					
					$filter_postcode = trim(sanitize_text_field($filters->postcode));
					$filter_distance = trim(intval($filters->distance));
					
					if ($filter_band = trim(sanitize_text_field($filters->band))) {
						$args['meta_query'][] = array(
							'key'		=> 'band_search',
							'compare'	=> 'LIKE',
							'value'		=> $filter_band,
						);
					}
					
					if ($filter_genre = trim(sanitize_text_field($filters->genre))) {
						$args['meta_query'][] = array(
							'key'		=> 'genre_search',
							'compare'	=> 'LIKE',
							'value'		=> ','.$filter_genre.',',
						);
					}
					
					if ($filter_venue = trim(sanitize_text_field($filters->venue))) {
						$args['meta_query'][] = array(
							'key'		=> 'venue_search',
							'compare'	=> 'LIKE',
							'value'		=> $filter_venue,
						);
					}
					
					if ($filter_town = trim(sanitize_text_field($filters->town))) {
						$args['meta_query'][] = array(
							'key'		=> 'town_search',
							'compare'	=> 'LIKE',
							'value'		=> $filter_town,
						);
					}
					
					$edit_link = get_permalink(1744);
					$type = 'mgevent';
					break;
			case 'mgvenue':
					$columns = array( 
						0 => 'title', 
						1 => 'town',
						2 => 'county',
						3 => 'entry_url'
					);
					if (in_array($col_no, array(0))) {
						$args['orderby'] = $columns[$col_no];
					} else if (in_array($col_no, array(1, 2))) {
						$args['orderby'] = 'meta_value_num';
						$args['meta_key'] = $columns[$col_no];
					}
					if ($filter_town = trim(sanitize_text_field($filters->town))) {
						$args['meta_query'][] = array(
							'key'		=> 'town',
							'compare'	=> 'LIKE',
							'value'		=> $filter_town,
						);
					}
					if ($filter_county = trim(sanitize_text_field($filters->county))) {
						$args['meta_query'][] = array(
							'key'		=> 'county',
							'compare'	=> 'LIKE',
							'value'		=> $filter_county,
						);
					}
					$edit_link = get_permalink(2256);
					$type = 'mgvenue';
					break;
		}
		
		if ($type) {
			$length = intval($request['length']);
			$start = intval($request['start']);
			$draw = intval($request['draw']);
			$args['fields'] = 'ids';
			$args['post_type'] = $type;
			$args['posts_per_page'] = $length;
			$args['offset'] = $start;
			$args['order'] = $order;
			
			if (array_key_exists('search_post_title', $args)) {
				add_filter('posts_where', array('GigTable', 'post_title_filter'), 10, 2);
			}
			$query = new WP_Query($args);
			if (array_key_exists('search_post_title', $args)) {
				remove_filter('posts_where', array('GigTable', 'post_title_filter'), 10, 2);
			}
			$totalRows = $query->found_posts;
		}
		
		if ($type && $query->have_posts() ) {		
			$rows = array();
			$perPage = $length?$length:50;
			while($query->have_posts()) {
				$query->the_post();
				$post_id = get_the_ID();
				$row = array();
				$entry_url = get_permalink();
				$options = '<a class="view-button" href="'.$entry_url.'"><i class="far fa-eye fa-fw"></i></a>';
				global $current_user;
				get_currentuserinfo();
				if (is_user_logged_in() && ($current_user->ID == get_the_author_meta( 'ID' ) || current_user_can('administrator') || current_user_can('edit_all_entries'))) {
					$options .= ' <a href="'.$edit_link.'?entry='.$post_id.'"><i class="far fa-edit fa-fw"></i></a>';	
				}
				switch ($type) {
					case 'mgband':
						$genre = get_field('genre');
						$row[] = get_the_title();
						$row[] = get_the_title($genre);
						$row[] = get_field('country');
						$row[] = $options;
						$rows[] = $row;
						break;
					case 'mgvenue':
						$county = get_field('county');
						$row[] = get_the_title(); 
						$row[] = get_field('town');
						$row[] = is_array($county)?"":$county;
						$row[] = '<div class="row-options">'.$options.'</div>';
						$rows[] = $row;
						break;
					case 'mgevent':
						if ($filter_postcode && $filter_distance) {
							$postcode_found = false;
						} else {
							$postcode_found = true;
						}
						
						$containerEnd = '</div>';
						$event_type = get_field('event_type');
						$event_name = get_field('event_name');
						$dates = null;
						$venues = null;
						$band_names = null;
						$towns = null;
						$genre_names = null;
						$last_venue_id = null;
						$same_venue = true;
						$event_types = null;
						$dates = null;
						if(have_rows('day', $post_id)) {
							$i=0;
							$day_count = count(get_field('day'));
							if ($day_count > 1) {
								$buttons = '<a class="prev-day" href="#">< Previous</a><a class="next-day show" href="#">Next ></a>';
							} else {
								$buttons = '';
							}
							while(have_rows('day', $post_id)) {
								the_row();
								$i++;
								$containerStart = '<div data-day="'.$i.'" class="match-height-container'.($i==1?' active':'').'">';
								if ($day_count > 1) {
									$dates .= $containerStart.'<div class="event_date match-height">'.date('d/m/Y', strtotime(get_sub_field('date'))).'</div>'.$containerEnd;
									$event_types .= $containerStart.'<div class="event_type match-height">'.$event_type.($day_count > 1?' - Day '.$i:'').'</div>'.$containerEnd;
								} else {
									$dates .= '<div class="event_date">'.date('d/m/Y', strtotime(get_sub_field('date'))).'</div>';
									$event_types .= '<div class="event_type">'.$event_type.'</div>';
								}
								$bands = get_sub_field('bands');
								$venue = get_sub_field('venue');
								$venue_name = get_the_title($venue);
								$town = get_field('town', $venue);
								
								if ($filter_distance) {
									$lat = get_field('lat', $venue);
									$lng = get_field('lng', $venue);
									
									if ($lat && $lng && $filter_postcode && $filter_distance && GigTable::isPostcodeInDistance($filter_postcode, $filter_distance, $lat, $lng)) {
										$postcode_found = true;
									}
								} else {
									$postcode_found = true;
								}
								if (!$postcode_found) {
									continue;
								}
								$entry_url = get_permalink($venue);
								$venues .= $containerStart.'<div class="venue match-height"><a href="'.$entry_url.'">'.$venue_name.'</a></div>'.$containerEnd;
								$towns .= $containerStart.'<div class="town match-height">'.$town.'</div>'."\n".$containerEnd;
								$band_names_inner = $event_name?'<div class="event-name">'.$event_name.'</div>':'';
								$genre_names_inner = $event_name?'<div class="event-name">&nbsp;</div>':'';
								if (count($bands)) {
									foreach($bands as $band) {
										setup_postdata($band);
										$band_name = get_the_title($band);
										
										$genre = get_field('genre', $band);
										$band_names_inner .= '<div class="band-name-row"><div class="band-name">'.get_the_title($band).'</div><div class="genre-name">'.get_the_title($genre).'</div></div>'."\n";
										//$genre_names_inner .= '<div class="genre-name">'.get_the_title($genre).'</div>'."\n";
									}
									if ($day_count > 1) {
										$current_band_names = '<div class="match-height">'.$band_names_inner.'</div>';
										//$current_genre_names = '<div class="match-height">'.$genre_names_inner.'</div>';
									} else {
										$current_band_names = $band_names_inner;
										//$current_genre_names = $genre_names_inner;
									}
									$band_names .= $day_count>1?$containerStart.$current_band_names.$containerEnd:$current_band_names;
									//$genre_names .= $day_count>1?$containerStart.$current_genre_names.$containerEnd:$current_genre_names;
								}
								if ($i > 1 && $last_venue_id != $venue->ID) {
									$same_venue = false;
								}
								$last_venue_id = $venue->ID;
							}
							if (!$postcode_found) {
								continue;
							}
							wp_reset_postdata();
						} else {
							continue;
						}
						if ($same_venue) {
							$venues = '<div class="venue"><a href="'.$entry_url.'">'.$venue_name.'</a></div>';
							$towns = '<div class="town">'.get_field('town', $venue).'</div>'."\n";;
						}
						
						if ($dates) {
							$row[] = $dates.$buttons;
							$row[] = $event_types;
							$row[] = $band_names;
							//$row[] = $genre_names;
							$row[] = $venues;
							$row[] = $towns;
							$row[] = $options;
							$rows[] = $row;
						}
						break;
				}

				if (count($rows) >= $perPage) {
					break;
				}
			}	
			wp_reset_query();
			
			if (count($rows)) { 
				$json_data = array(
					"draw" => $draw,
					"recordsTotal" => intval($totalRows),
					"recordsFiltered" => intval($totalRows),
					"data" => $rows
				);
			} else {
				$json_data = array(
					"draw" => 0,
					"recordsTotal" => 0,
					"recordsFiltered" => 0,
					"data" => [],
				);
			}
			echo json_encode($json_data);		 
		} else {
			 
			$json_data = array(
				"draw" => 0,
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => [],
			);
			 
			echo json_encode($json_data);
		}
		wp_die();
	}
	
}
?>