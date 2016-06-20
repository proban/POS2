<?php
include_once('../../../config/config.php');

class db_config {

	public static function get_db_server() {
		global $DB_SERVER;
		return $DB_SERVER;
	}

	public static function get_db_user() {
		global $DB_USER;
		return $DB_USER;
	}

	public static function get_db_pass() {
		global $DB_PASS;
		return $DB_PASS;
	}
	
	public static function get_db_name() {
		global $DB_NAME;
		return $DB_NAME;
	}

	public static function get_branch_id() {
		global $BRANCH_ID;
		return $BRANCH_ID;
	}
}
?>