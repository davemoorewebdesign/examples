<?php
// Surpress unnecessary errors
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

// Initalise App
require_once('framework/App.php');
$app = new App();