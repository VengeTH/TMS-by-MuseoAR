<?php
// ! work in progress
require_once realpath(__DIR__ . "/vendor/autoload.php");

// Looing for .env at the root directory
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
?>
