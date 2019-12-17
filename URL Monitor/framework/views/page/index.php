<h1><?php echo $this->getMetaTitle(); ?></h1>
<div class="page-container">
	<h3>Choose a page:</h3>
	<ul class="page-links">
		<?php
		$pages = $this->pageData['pages'];
		foreach($pages as $page) {
			?>
			<li class="page-link"><a href="<?php echo $this->app->getBaseUrl(); ?>?page=<?php echo $page->slug; ?>"><?php echo $page->name; ?></a></li>
			<?php
		}
		?>
	</ul>
</div>