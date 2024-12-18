<?php
require_once dirname(__DIR__) . "/db/db.php";
class user extends db {
	public function __construct() {
		parent::__construct();
	}
	public function __destruct() {
		parent::__destruct();
	}
}
?>