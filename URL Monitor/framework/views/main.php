<!doctype html>
<html lang="en">
<head>
	<title><?php echo $this->getMetaTitle(); ?></title>
	<link rel="stylesheet" href="<?php echo $this->app->getBaseUrl(); ?>/assets/css/main.css">
	<script src="<?php echo $this->app->getBaseUrl(); ?>/assets/js/jquery-3.4.1.min.js"></script>
	<script src="<?php echo $this->app->getBaseUrl(); ?>/assets/js/app.js"></script>
</head>
<body>
	<div id="content">
		<?php echo $this->content; ?>
	</div>
</body>
</html>