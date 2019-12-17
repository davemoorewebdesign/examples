<?php
// Get page dat passed from controller
$page = $this->pageData['page'];

// Get all Trafficlights associated with this page
$trafficLights = $page->getTrafficlights();

// Prepare Trafficlight data ready for passing to the JS
$trafficlightsData = array();
foreach($trafficLights as $trafficLight) {
	$trafficlightsData[] = array(
		'id' => $trafficLight->id,
		'name' => $trafficLight->name,
		'url' => $trafficLight->url,
		'frequency' => $trafficLight->frequency,
	);
}
?>
<script type="text/javascript">
	var trafficlightsData = <?php echo json_encode($trafficlightsData); ?>;
</script>

<h1><?php echo $page->name; ?></h1>

<div class="trafficlights-container col-container"></div>

<div class="back-button-container">
	<a href="<?php echo $this->app->getBaseUrl(); ?>">&lt;&lt; Back</a>
</div>