<?php
// ! work in progress
require_once realpath(dirname(__DIR__) . "/vendor/autoload.php");

// Looing for .env at the root directory
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
?>
